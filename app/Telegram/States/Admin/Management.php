<?php

namespace App\Telegram\States\Admin;

use App\Enums\Telegram\StateKey;
use App\Models\User;
use App\Telegram\Core\AbstractState;

class Management extends AbstractState
{
    public function onEnter(): void
    {
        if (!$this->ensureAdmin()) {
            return;
        }

        $this->sendT('telegram.admin.management_intro', $this->mainMenuKeyboard());
    }

    public function onText(string $text, array $update): void
    {
        if (!$this->ensureAdmin()) {
            return;
        }

        $this->sendT('telegram.admin.management_intro', $this->mainMenuKeyboard());
    }

    private function ensureAdmin(): ?User
    {
        $record = $this->process();

        if (!$record instanceof User || !$record->is_admin) {
            $this->goEnum(StateKey::Welcome);
            return null;
        }

        return $record;
    }
}

