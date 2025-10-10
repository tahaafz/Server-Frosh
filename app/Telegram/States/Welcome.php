<?php

namespace App\Telegram\States;

use App\Telegram\Core\AbstractState;
use App\Telegram\UI\KeyboardFactory;

class Welcome extends AbstractState
{
    public function onEnter(): void
    {
        $this->send(__('telegram.welcome'), KeyboardFactory::replyMain());
    }
}
