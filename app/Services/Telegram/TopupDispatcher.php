<?php
namespace App\Services\Telegram;

use App\Models\TopupRequest;
use App\Models\User;
use App\Traits\Telegram\TgApi;

class TopupDispatcher
{
    use TgApi;

    public function sendToAdmins(TopupRequest $req): void
    {
        $cap = "🧾 درخواست شارژ کیف پول\n"
            . "UserID: <code>{$req->user_id}</code>\n"
            . "مبلغ: <b>".number_format($req->amount)."</b> تومان\n"
            . "روش: <code>{$req->method}</code>\n"
            . "ID: <code>{$req->id}</code>";

        $kb = [
            'inline_keyboard' => [
                [ ['text'=>'✅ تایید', 'callback_data'=>"topup:approve:{$req->id}"],
                    ['text'=>'❌ رد',   'callback_data'=>"topup:reject:{$req->id}"] ],
            ]
        ];

        $admins = User::query()->where('is_admin',true)->whereNotNull('telegram_chat_id')->get();
        foreach ($admins as $a) {
            if ($req->receipt_file_id) {
                $this->tgSendPhoto($a->telegram_chat_id, $req->receipt_file_id, $cap, $kb);
            } else {
                $this->tgSend($a->telegram_chat_id, $cap, $kb);
            }
        }
    }
}
