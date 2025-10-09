<?php

namespace App\Telegram\UI;

use App\Support\Telegram\Msg;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

trait ManagesScreens
{
    protected function hideReplyKeyboardOnce(): void
    {
        $user = $this->process();

        try {
            $response = Telegram::sendMessage([
                'chat_id' => $user->telegram_chat_id,
                'text' => "\u{200B}",
                'reply_markup' => json_encode(['remove_keyboard' => true]),
                'disable_notification' => true,
            ]);
        } catch (TelegramResponseException $exception) {
            if (str_contains($exception->getMessage(), 'text must be non-empty')) {
                $response = Telegram::sendMessage([
                    'chat_id' => $user->telegram_chat_id,
                    'text' => '.',
                    'reply_markup' => json_encode(['remove_keyboard' => true]),
                    'disable_notification' => true,
                ]);
            } else {
                throw $exception;
            }
        }

        $messageId = data_get($response, 'message_id') ?? data_get($response, 'messageId');
        if (! $messageId) {
            return;
        }

        if (method_exists($this, 'tgDelete')) {
            $this->tgDelete($user->telegram_chat_id, (int) $messageId);

            return;
        }

        Telegram::deleteMessage([
            'chat_id' => $user->telegram_chat_id,
            'message_id' => $messageId,
        ]);
    }

    protected function sendAnchor(string $textOrKey, array $inlineKeyboard): void
    {
        $user = $this->process();

        $response = Telegram::sendMessage([
            'chat_id' => $user->telegram_chat_id,
            'text' => Msg::resolve($textOrKey),
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);

        $messageId = data_get($response, 'message_id') ?? data_get($response, 'messageId');

        $user->tg_last_message_id = $messageId ? (int) $messageId : null;
        $user->save();
    }

    protected function ensureInlineScreen(string $textOrKey, array $inlineKeyboard, bool $resetAnchor = false): void
    {
        $user = $this->process();

        if ($resetAnchor && $user->tg_last_message_id) {
            $this->deleteAnchor($user->telegram_chat_id, $user->tg_last_message_id);

            $user->tg_last_message_id = null;
            $user->save();
        }

        if ($user->tg_last_message_id) {
            Telegram::editMessageText([
                'chat_id' => $user->telegram_chat_id,
                'message_id' => $user->tg_last_message_id,
                'text' => Msg::resolve($textOrKey),
                'reply_markup' => json_encode($inlineKeyboard),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            return;
        }

        $this->sendAnchor($textOrKey, $inlineKeyboard);
    }

    protected function resetToWelcomeMenu(): void
    {
        $user = $this->process();

        if ($user->tg_last_message_id) {
            $this->deleteAnchor($user->telegram_chat_id, $user->tg_last_message_id);

            $user->tg_last_message_id = null;
            $user->save();
        }

        $this->send(
            __('telegram.welcome'),
            KeyboardFactory::replyMain($user),
            trackMessage: false
        );
    }

    private function deleteAnchor(int|string $chatId, int $messageId): void
    {
        if (method_exists($this, 'tgDelete')) {
            $this->tgDelete($chatId, $messageId);

            return;
        }

        Telegram::deleteMessage([
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }
}
