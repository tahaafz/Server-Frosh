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
        $text   = $method?->instruction($req) ?? "لطفاً رسید پرداخت را ارسال کنید.";
        $kb     = $method?->keyboard($req);

        $this->send($text, $kb);
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) return;
        $this->send("برای ادامه، لطفاً <b>عکس رسید</b> را ارسال کنید.");
    }

    public function onPhoto(array $photos, array $u): void
    {
        $last   = $photos[array_key_last($photos)] ?? null;
        $fileId = $last['file_id'] ?? null;
        if (!$fileId) { $this->send("❗️دریافت عکس نامعتبر بود. دوباره ارسال کنید."); return; }

        $p   = $this->process();
        $req = TopupRequest::where('user_id',$p->id)->where('status','pending')->latest()->first();
        if (!$req) { $this->send("❗️درخواست شارژ یافت نشد. دوباره تلاش کنید."); return; }

        $req->receipt_file_id = $fileId;
        $req->save();

        app(AdminMessenger::class)->broadcastTopupRequest($req);

        $this->send("✅ رسید شما دریافت شد. پس از بررسی ادمین، نتیجه اطلاع داده می‌شود.");

        $this->parent->transitionTo(StateKey::Welcome->value);
    }

    public function onCallback(string $data, array $u): void
    {
        if (preg_match('~^topup:cancel:(\d+)$~', $data, $m)) {
            $id  = (int)$m[1];
            $p   = $this->process();
            $req = TopupRequest::where('id',$id)->where('user_id',$p->id)->where('status','pending')->first();
            if ($req) {
                $req->status = 'canceled'; $req->save();
                $this->send("❎ درخواست شارژ لغو شد.");
                $this->parent->transitionTo(StateKey::Welcome->value);
                return;
            }
        }

        $this->onEnter();
    }
}
