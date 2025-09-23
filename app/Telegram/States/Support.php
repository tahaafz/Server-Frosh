<?php

namespace App\Telegram\States;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;

class Support extends State
{
    use ReadsUpdate, SendsMessages;

    public function onEnter(): void
    {
        $this->send("🛠 پشتیبانی — پیام‌تان را بنویسید.\nبرای بازگشت: /back");
    }

    public function onText(string $text, array $u): void
    {
        if (in_array($text, ['/back','back','برگشت'])) {
            $this->parent->transitionTo('welcome'); return;
        }
        $this->send("پیام دریافت شد ✅");
    }
}
