<?php

namespace App\Telegram\States;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;

class Support extends State
{
    use ReadsUpdate, SendsMessages;

    public function onEnter(): void
    {
        $this->send(__('telegram.support.prompt'));
    }

    public function onText(string $text, array $u): void
    {
        if (in_array($text, ['/back','back', \App\Telegram\UI\Buttons::label('back')])) {
            $this->parent->transitionTo(StateKey::Welcome->value); return;
        }
        $this->send(__('telegram.support.received'));
    }
}
