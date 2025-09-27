<?php

namespace App\Gurds\Telegram;

use App\Traits\Telegram\TgApi;
use Telegram\Bot\Laravel\Facades\Telegram;

class ChannelGate
{
    use TgApi;

    public function isChannelLockOn(): bool
    {
        return strtolower((string) config('telegram.channel.lock')) === 'on';
    }

    public function chatIdForApi(): string
    {
        $link = config('telegram.channel.link');
        return $link ? '@'.$link : '';
    }

    public function isMember(int|string $telegramUserId): bool
    {
        $chat = $this->chatIdForApi();
        if (!$chat) return false;

        try {
            $res = Telegram::getChatMember(['chat_id'=>$chat,'user_id'=>$telegramUserId]);
            $status = data_get($res, 'status');
            return in_array($status, ['member','administrator','creator','restricted'], true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function sendJoinPrompt(int|string $chatId): void
    {
        $link = config('telegram.channel.link');
        $url  = $link ? "https://t.me/{$link}" : null;

        $kb = ['inline_keyboard' => array_filter([
            $url ? [ [ 'text' => 'عضویت در کانال', 'url' => $url ] ] : null,
            [ [ 'text' => '✅ عضو شدم، بررسی کن', 'callback_data' => 'confirm:channel' ] ],
        ])];

        $this->tgSend($chatId,
            "برای ادامه لازم است در کانال ما عضو باشید.\nپس از عضویت، روی «عضو شدم، بررسی کن» بزنید.",
            $kb
        );
    }

    public function confirmOrAlert(array $update, int|string $chatId, int|string $telegramUserId): bool
    {
        if ($this->isMember($telegramUserId)) {
            if ($mid = data_get($update, 'callback_query.message.message_id')) {
                $this->tgDelete($chatId, (int)$mid);
            }
            return true;
        }

        if ($cbId = data_get($update, 'callback_query.id')) {
            $this->tgToast($cbId, 'هنوز عضو کانال نیستید.', true, 3);
        }
        return false;
    }
}
