<?php
namespace App\Telegram\States\Wallet;

use App\Jobs\Telegram\ArchiveTelegramPhotoJob;
use App\Models\TopupRequest;
use App\Payments\PaymentRegistry;
use App\Services\Telegram\Admin\AdminMessenger;
use App\Services\Telegram\TopupDispatcher;

class WaitReceipt extends \App\Telegram\Core\AbstractState
{

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

        if (is_array($kb) && isset($kb['inline_keyboard'])) {
            $kb['inline_keyboard'] = array_map(function ($row) {
                return array_map(function ($btn) {
                    if (isset($btn['callback_data'])) $btn['callback_data'] = $this->pack((string)$btn['callback_data']);
                    return $btn;
                }, $row);
            }, $kb['inline_keyboard']);
        }

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
        $uniqId = $last['file_unique_id'] ?? null;

        if (!$fileId) { $this->sendT('telegram.wallet.invalid_photo'); return; }

        $p   = $this->process();
        $req = TopupRequest::where('user_id',$p->id)->where('status','pending')->latest()->first();
        if (!$req) { $this->sendT('telegram.wallet.request_not_found'); return; }

        $req->receipt_file_id = $fileId;
        $req->save();
        ArchiveTelegramPhotoJob::dispatch(
            userId: $p->id,
            tgFileId: $fileId,
            tgUniqueId: $uniqId,
            purpose: 'receipt',
            mediableType: \App\Models\TopupRequest::class,
            mediableId: $req->id
        );
        app(AdminMessenger::class)->broadcastTopupRequest($req);

        $this->sendT('telegram.wallet.received');

        $this->goEnum(\App\Enums\Telegram\StateKey::Welcome);
    }

    public function onCallback(string $data, array $u): void
    {
        $parsed = $this->cbParse($data, $u);
        if ($parsed && $parsed['action'] === \App\Telegram\Callback\Action::TopupCancel) {
            $id  = (int)($parsed['params']['id'] ?? 0);
            $p   = $this->process();
            $req = TopupRequest::where('id',$id)->where('user_id',$p->id)->where('status','pending')->first();
            if ($req) {
                $req->status = 'canceled'; $req->save();
                $this->sendT('telegram.wallet.canceled');
                $this->goEnum(\App\Enums\Telegram\StateKey::Welcome);
                return;
            }
        }

        $this->onEnter();
    }
}
