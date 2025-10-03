<?php

namespace App\Telegram\States;

class Support extends \App\Telegram\Core\AbstractState
{
    public function onEnter(): void
    {
        $this->sendT('telegram.support.prompt');
    }

    public function onText(string $text, array $u): void
    {
        if (in_array($text, ['/back','back', \App\Telegram\UI\Buttons::label('back')])) {
            $this->goEnum(\App\Enums\Telegram\StateKey::Welcome); return;
        }
        $this->sendT('telegram.support.received');
    }
}
