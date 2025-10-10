<?php

namespace App\Telegram\States;

use App\Services\Telegram\Admin\AdminMessenger;
use App\Telegram\Core\AbstractState;
use App\Telegram\UI\KeyboardFactory;

class Support extends AbstractState
{
    public function onEnter(): void
    {
        $this->expireInlineScreen(); // اگر از خرید آمد، لنگر اینلاین را ببند
        $this->sendWithReplyKeyboard('telegram.support.enter', \App\Telegram\UI\KeyboardFactory::replyBackOnly());
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) return;

        app(AdminMessenger::class)->broadcastSupportFromUser($this->process(), $text);
        $this->sendWithReplyKeyboard('telegram.support.sent', \App\Telegram\UI\KeyboardFactory::replyBackOnly());
    }
}
