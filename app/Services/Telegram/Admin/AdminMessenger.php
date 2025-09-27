<?php

namespace App\Services\Telegram\Admin;

use App\Models\TopupRequest;
use App\Models\User;
use App\Traits\Telegram\TgApi;

class AdminMessenger
{
    use TgApi;

    public function broadcastTopupRequest(TopupRequest $req): void
    {
        $cap = "🧾 درخواست شارژ کیف پول\n"
            . "UserID: <code>{$req->user_id}</code> • Amount: <b>".number_format($req->amount)."</b> تومان\n"
            . "Method: <code>{$req->method}</code> • ID: <code>{$req->id}</code>";

        $kb = [
            'inline_keyboard' => [
                [
                    ['text'=>'✅ تایید','callback_data'=>"topup:approve:{$req->id}"],
                    ['text'=>'❌ رد','callback_data'=>"topup:reject:{$req->id}"],
                ],
            ],
        ];

        User::query()->where('is_admin',true)->whereNotNull('telegram_chat_id')->chunkById(200, function($admins) use ($req,$cap,$kb) {
            foreach ($admins as $a) {
                if ($req->receipt_file_id) {
                    $this->tgSendPhoto($a->telegram_chat_id, $req->receipt_file_id, $cap, $kb);
                } else {
                    $this->tgSend($a->telegram_chat_id, $cap, $kb);
                }
            }
        });
    }

    public function broadcastSupportFromUser(User $from, string $text, ?string $photoFileId = null): void
    {
        $cap = "📩 پیام پشتیبانی از کاربر\n"
            . "User: <code>{$from->id}</code> • TG: <code>{$from->telegram_user_id}</code>\n\n"
            . $text;

        $kb = [
            'inline_keyboard' => [
                [ ['text'=>'✍️ پاسخ','callback_data'=>"admin:reply:start:{$from->id}"] ],
            ],
        ];

        User::query()->where('is_admin',true)->whereNotNull('telegram_chat_id')->chunkById(200, function($admins) use ($cap,$kb,$photoFileId) {
            foreach ($admins as $a) {
                if ($photoFileId) {
                    $this->tgSendPhoto($a->telegram_chat_id, $photoFileId, $cap, $kb);
                } else {
                    $this->tgSend($a->telegram_chat_id, $cap, $kb);
                }
            }
        });
    }

    public function notifyAll(string $html): void
    {
        User::query()->where('is_admin',true)->whereNotNull('telegram_chat_id')->chunkById(200, function($admins) use ($html) {
            foreach ($admins as $a) $this->tgSend($a->telegram_chat_id, $html);
        });
    }
}
