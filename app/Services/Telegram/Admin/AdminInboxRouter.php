<?php

namespace App\Services\Telegram\Admin;

use App\DTOs\Telegram\TelegramUpdateDTO;
use App\Models\User;
use App\Services\Telegram\TopupApprovalService;
use App\Traits\Telegram\TgApi;
use Illuminate\Support\Facades\Cache;

class AdminInboxRouter
{
    use TgApi;

    public function maybeHandle(User $actor, TelegramUpdateDTO $dto): bool
    {
        if (!$actor->is_admin) {
            return false;
        }

        if ($dto->cbData && preg_match('~^topup:(approve|reject):(\d+)$~', $dto->cbData, $m)) {
            app(TopupApprovalService::class)->handle($actor, $m[1], (int)$m[2]);
            if ($cbId = data_get($dto->raw,'callback_query.id')) {
                $this->tgToast($cbId, $m[1]==='approve'?'تایید شد':'رد شد', false, 1);
            }
            return true;
        }

        if ($dto->cbData && preg_match('~^admin:reply:start:(\d+)$~', $dto->cbData, $m)) {
            $targetUserId = (int)$m[1];
            Cache::put("admin:reply:target:{$actor->id}", $targetUserId, now()->addMinutes(10));
            $this->tgSend($actor->telegram_chat_id, "✍️ پاسخ خود را بنویسید (متن یا عکس).");
            return true;
        }

        if ($dto->text && ($target = Cache::get("admin:reply:target:{$actor->id}"))) {
            $user = User::find($target);
            if ($user && $user->telegram_chat_id) {
                $this->tgSend($user->telegram_chat_id, "🛠 پاسخ پشتیبانی:\n".$dto->text);
                $this->tgSend($actor->telegram_chat_id, "✅ پاسخ ارسال شد.");
            }
            Cache::forget("admin:reply:target:{$actor->id}");
            return true;
        }

        if (($photos = data_get($dto->raw,'message.photo')) && ($target = Cache::get("admin:reply:target:{$actor->id}"))) {
            $user = User::find($target);
            $last = $photos[array_key_last($photos)] ?? null;
            $fileId = $last['file_id'] ?? null;

            if ($user && $user->telegram_chat_id && $fileId) {
                $this->tgSendPhoto($user->telegram_chat_id, $fileId, "🛠 پاسخ پشتیبانی (تصویر)");
                $this->tgSend($actor->telegram_chat_id, "✅ پاسخ تصویری ارسال شد.");
            } else {
                $this->tgSend($actor->telegram_chat_id, "❗️ ارسال تصویر نامعتبر بود.");
            }
            Cache::forget("admin:reply:target:{$actor->id}");
            return true;
        }

        return false;
    }
}
