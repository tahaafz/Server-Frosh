<?php
namespace App\Telegram\States\Wallet;

use App\Enums\Telegram\StateKey;
use App\Models\TopupRequest;
use App\Payments\PaymentRegistry;
use App\Services\Telegram\Admin\AdminMessenger;
use App\Telegram\Core\State;
use App\Traits\Telegram\{ReadsUpdate,SendsMessages,PersistsData,MainMenuShortcuts,FlowToken};
use App\Services\Telegram\TopupDispatcher;

class WaitReceipt extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $p   = $this->process();
        $amt = (int)$this->getData('topup_amount', 0);
        $met = (string)$this->getData('topup_method', 'card');

            $req = TopupRequest::create([
                'user_id' => $p->id,
                'method'  => $met,
                'amount'  => $amt,
                'currency'=> 'IRT',
                'status'  => 'pending',
            ]);

        $method = PaymentRegistry::byKey($met);
        $text   = $method?->instruction($req) ?? __('telegram.wallet.send_receipt');
        $kb     = $method?->keyboard($req);

        $this->send($text, $kb);
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) return;
        $this->send(__('telegram.wallet.send_receipt'));
    }

    public function onPhoto(array $photos, array $u): void
    {
        $last   = $photos[array_key_last($photos)] ?? null;
        $fileId = $last['file_id'] ?? null;
        if (!$fileId) { $this->send(__('telegram.wallet.invalid_photo')); return; }

        $p   = $this->process();
        $req = TopupRequest::where('user_id',$p->id)->where('status','pending')->latest()->first();
        if (!$req) { $this->send(__('telegram.wallet.request_not_found')); return; }

        $req->receipt_file_id = $fileId;
        $req->save();

        app(AdminMessenger::class)->broadcastTopupRequest($req);

        $this->send(__('telegram.wallet.received'));

        $this->parent->transitionTo(StateKey::Welcome->value);
    }

    public function onCallback(string $data, array $u): void
    {
        // این پیام‌ها FlowToken ندارند؛ مستقیم parse می‌کنیم
        $parsed = \App\Telegram\Callback\CallbackData::parse($data);
        if ($parsed && $parsed['action'] === \App\Telegram\Callback\Action::TopupCancel) {
            $id  = (int)($parsed['params']['id'] ?? 0);
            $p   = $this->process();
            $req = TopupRequest::where('id',$id)->where('user_id',$p->id)->where('status','pending')->first();
            if ($req) {
                $req->status = 'canceled'; $req->save();
                $this->send(__('telegram.wallet.canceled'));
                $this->parent->transitionTo(StateKey::Welcome->value);
                return;
            }
        }

        $this->onEnter();
    }
}
