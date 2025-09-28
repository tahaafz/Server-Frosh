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

        if ($dto->cbData) {
            if ($p = \App\Telegram\Callback\CallbackData::parse($dto->cbData)) {
                if ($p['action'] === \App\Telegram\Callback\Action::TopupApprove || $p['action'] === \App\Telegram\Callback\Action::TopupReject) {
                    $id = (int)($p['params']['id'] ?? 0);
                    $act = $p['action'] === \App\Telegram\Callback\Action::TopupApprove ? 'approve' : 'reject';
                    app(TopupApprovalService::class)->handle($actor, $act, $id);
                    if ($cbId = data_get($dto->raw,'callback_query.id')) {
                        $this->tgToast(
                            $cbId,
                            $act === 'approve' ? __('telegram.admin.approved') : __('telegram.admin.rejected'),
                            false,
                            1
                        );
                    }
                    return true;
                }
                if ($p['action'] === \App\Telegram\Callback\Action::AdminReplyStart) {
                    $targetUserId = (int)($p['params']['user'] ?? 0);
                    Cache::put("admin:reply:target:{$actor->id}", $targetUserId, now()->addMinutes(10));
                    $this->tgSend($actor->telegram_chat_id, __('telegram.admin.reply_prompt'));
                    return true;
                }
            }
        }

        if ($dto->text && ($target = Cache::get("admin:reply:target:{$actor->id}"))) {
            $user = User::find($target);
            if ($user && $user->telegram_chat_id) {
                $this->tgSend($user->telegram_chat_id, __('telegram.admin.support_reply_prefix')."\n".$dto->text);
                $this->tgSend($actor->telegram_chat_id, __('telegram.admin.reply_sent'));
            }
            Cache::forget("admin:reply:target:{$actor->id}");
            return true;
        }

        if (($photos = data_get($dto->raw,'message.photo')) && ($target = Cache::get("admin:reply:target:{$actor->id}"))) {
            $user = User::find($target);
            $last = $photos[array_key_last($photos)] ?? null;
            $fileId = $last['file_id'] ?? null;

            if ($user && $user->telegram_chat_id && $fileId) {
                $this->tgSendPhoto($user->telegram_chat_id, $fileId, __('telegram.admin.support_reply_photo'));
                $this->tgSend($actor->telegram_chat_id, __('telegram.admin.reply_photo_sent'));
            } else {
                $this->tgSend($actor->telegram_chat_id, __('telegram.admin.invalid_photo'));
            }
            Cache::forget("admin:reply:target:{$actor->id}");
            return true;
        }

        return false;
    }
}
