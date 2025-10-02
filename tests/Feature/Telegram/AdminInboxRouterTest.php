<?php

use App\DTOs\Telegram\TelegramUpdateDTO;
use App\Models\User;
use App\Services\Telegram\Admin\AdminInboxRouter;
use App\Telegram\Callback\Action;
use App\Telegram\Callback\CallbackData;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('forwards admin text replies to the selected user', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'telegram_user_id' => 501,
        'telegram_chat_id' => 501,
    ]);

    $target = User::factory()->create([
        'telegram_user_id' => 1001,
        'telegram_chat_id' => 1001,
    ]);

    $router = new class extends AdminInboxRouter {
        public array $messages = [];
        public array $photos = [];

        protected function tgSend(int|string $chatId, string $text, ?array $replyMarkup = null, string $parseMode = 'HTML'): ?object
        {
            $this->messages[] = compact('chatId', 'text', 'replyMarkup', 'parseMode');

            return (object)['messageId' => count($this->messages)];
        }

        protected function tgSendPhoto(int|string $chatId, string $fileId, string $caption = '', ?array $replyMarkup = null, string $parseMode = 'HTML'): ?object
        {
            $this->photos[] = compact('chatId', 'fileId', 'caption', 'replyMarkup', 'parseMode');

            return (object)['messageId' => count($this->photos)];
        }

        protected function tgToast(string $callbackQueryId, string $text, bool $alert = false, int $cacheTime = 0): void
        {
            // not required for these tests
        }
    };

    $callbackDto = TelegramUpdateDTO::from([
        'update_id' => 1,
        'callback_query' => [
            'id' => 'abc',
            'from' => [
                'id' => $admin->telegram_user_id,
                'first_name' => 'Admin',
            ],
            'message' => [
                'chat' => [
                    'id' => $admin->telegram_chat_id,
                    'type' => 'private',
                ],
            ],
            'data' => CallbackData::build(Action::AdminReplyStart, ['user' => $target->id]),
        ],
    ]);

    expect($router->maybeHandle($admin->fresh(), $callbackDto))->toBeTrue();

    $textDto = TelegramUpdateDTO::from([
        'update_id' => 2,
        'message' => [
            'from' => [
                'id' => $admin->telegram_user_id,
                'first_name' => 'Admin',
            ],
            'chat' => [
                'id' => $admin->telegram_chat_id,
                'type' => 'private',
            ],
            'text' => 'پیام تستی',
        ],
    ]);

    expect($router->maybeHandle($admin->fresh(), $textDto))->toBeTrue();

    expect($router->messages)->toHaveCount(3);
    expect($router->messages[0]['chatId'])->toBe($admin->telegram_chat_id);
    expect($router->messages[0]['text'])->toContain(__('telegram.admin.reply_prompt'));
    expect($router->messages[1]['chatId'])->toBe($target->telegram_chat_id);
    expect($router->messages[1]['text'])->toContain(__('telegram.admin.support_reply_prefix'));
    expect($router->messages[2]['chatId'])->toBe($admin->telegram_chat_id);
    expect($router->messages[2]['text'])->toBe(__('telegram.admin.reply_sent'));

    expect($admin->fresh()->tg_data['admin_reply_target'] ?? null)->toBeNull();
});

it('forwards admin photo replies with acknowledgement', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'telegram_user_id' => 601,
        'telegram_chat_id' => 601,
    ]);

    $target = User::factory()->create([
        'telegram_user_id' => 1601,
        'telegram_chat_id' => 1601,
    ]);

    $router = new class extends AdminInboxRouter {
        public array $messages = [];
        public array $photos = [];

        protected function tgSend(int|string $chatId, string $text, ?array $replyMarkup = null, string $parseMode = 'HTML'): ?object
        {
            $this->messages[] = compact('chatId', 'text', 'replyMarkup', 'parseMode');

            return (object)['messageId' => count($this->messages)];
        }

        protected function tgSendPhoto(int|string $chatId, string $fileId, string $caption = '', ?array $replyMarkup = null, string $parseMode = 'HTML'): ?object
        {
            $this->photos[] = compact('chatId', 'fileId', 'caption', 'replyMarkup', 'parseMode');

            return (object)['messageId' => count($this->photos)];
        }

        protected function tgToast(string $callbackQueryId, string $text, bool $alert = false, int $cacheTime = 0): void
        {
            // not required for these tests
        }
    };

    $callbackDto = TelegramUpdateDTO::from([
        'update_id' => 10,
        'callback_query' => [
            'id' => 'def',
            'from' => [
                'id' => $admin->telegram_user_id,
                'first_name' => 'Admin',
            ],
            'message' => [
                'chat' => [
                    'id' => $admin->telegram_chat_id,
                    'type' => 'private',
                ],
            ],
            'data' => CallbackData::build(Action::AdminReplyStart, ['user' => $target->id]),
        ],
    ]);

    expect($router->maybeHandle($admin->fresh(), $callbackDto))->toBeTrue();

    $photoDto = TelegramUpdateDTO::from([
        'update_id' => 11,
        'message' => [
            'from' => [
                'id' => $admin->telegram_user_id,
                'first_name' => 'Admin',
            ],
            'chat' => [
                'id' => $admin->telegram_chat_id,
                'type' => 'private',
            ],
            'photo' => [
                ['file_id' => 'file_preview'],
                ['file_id' => 'file_123'],
            ],
        ],
    ]);

    expect($router->maybeHandle($admin->fresh(), $photoDto))->toBeTrue();

    expect($router->photos)->toHaveCount(1);
    expect($router->photos[0]['chatId'])->toBe($target->telegram_chat_id);
    expect($router->photos[0]['fileId'])->toBe('file_123');
    expect($router->messages)->toHaveCount(2);
    expect($router->messages[1]['chatId'])->toBe($admin->telegram_chat_id);
    expect($router->messages[1]['text'])->toBe(__('telegram.admin.reply_photo_sent'));

    expect($admin->fresh()->tg_data['admin_reply_target'] ?? null)->toBeNull();
});
