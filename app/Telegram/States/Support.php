<?php

namespace App\Telegram\States;

use App\Enums\Telegram\StateKey;
use App\Services\Telegram\Admin\AdminMessenger;
use App\Telegram\UI\Buttons;

use function e;

class Support extends \App\Telegram\Core\AbstractState
{
    public function onEnter(): void
    {
        $this->newFlow();
        $this->sendT('telegram.support.prompt');
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) {
            return;
        }

        if ($this->isBackCommand($text)) {
            $this->goEnum(StateKey::Welcome);
            return;
        }

        $message = trim($text);
        if ($message === '') {
            $this->sendT('telegram.support.empty_message');
            return;
        }

        $safeMessage = e($message);

        $this->messenger()->broadcastSupportFromUser($this->process(), $safeMessage);

        $this->sendT('telegram.support.received', $this->mainMenuKeyboard());
    }

    public function onPhoto(array $photos, array $u): void
    {
        $last   = $photos[array_key_last($photos)] ?? null;
        $fileId = $last['file_id'] ?? null;

        if (!$fileId) {
            $this->sendT('telegram.support.invalid_photo');
            return;
        }

        $caption = (string) data_get($u, 'message.caption', '');
        $message = trim($caption);
        if ($message === '') {
            $message = __('telegram.support.photo_without_caption');
        }

        $safeMessage = e($message);

        $this->messenger()->broadcastSupportFromUser($this->process(), $safeMessage, $fileId);

        $this->sendT('telegram.support.received_photo', $this->mainMenuKeyboard());
    }

    protected function isBackCommand(string $text): bool
    {
        $normalized = trim($text);

        if ($normalized === Buttons::label('back')) {
            return true;
        }

        $lower = mb_strtolower($normalized);

        return in_array($lower, ['back', '/back'], true);
    }

    protected function messenger(): AdminMessenger
    {
        return app(AdminMessenger::class);
    }
}
