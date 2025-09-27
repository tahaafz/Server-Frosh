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
        $this->send("🛠 پشتیبانی — پیام‌تان را بنویسید.\nبرای بازگشت: /back");
    }

    public function onText(string $text, array $u): void
    {
        if (in_array($text, ['/back','back','برگشت'])) {
            $this->parent->transitionTo(StateKey::Welcome->value); return;
        }
        $this->send("پیام دریافت شد ✅");
    }
}
