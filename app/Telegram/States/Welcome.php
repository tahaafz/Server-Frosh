<?php

namespace App\Telegram\States;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;

class Welcome extends State
{
    use ReadsUpdate, SendsMessages;

    public function onEnter(): void
    {
        $this->send(
            "به ربات خوش آمدید 👋\nلطفاً یکی از گزینه‌ها را انتخاب کنید:",
            $this->replyKeyboard([ ['خرید VPS', 'پشتیبانی', 'مدیریت سرورها'] ])
        );
    }

    public function onText(string $text, array $u): void
    {
        if ($text === 'خرید vps' || str_contains($text,'خرید') || str_contains($text,'vps')) {
            $this->parent->transitionTo('buy.choose_provider'); return;
        }
        if ($text === 'پشتیبانی' || str_contains($text,'support')) {
            $this->parent->transitionTo('support'); return;
        }
        if (str_contains($text,'مدیریت')) {
            $this->parent->transitionTo('servers.list'); return;
        }
        $this->onEnter();
    }
}
