<?php

namespace App\Telegram\States;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;
use APP\Support\Telegram\Text;

class Welcome extends State
{
    use ReadsUpdate, SendsMessages;

    public function onEnter(): void
    {
        $this->send(
            "به ربات خوش آمدید 👋\nلطفاً یکی از گزینه‌ها را انتخاب کنید:",
            $this->replyKeyboard([ ['خرید VPS', 'پشتیبانی', 'مدیریت سرورها'], ['افزایش موجودی'] ])
        );
    }

    public function onText(string $text, array $u): void
    {
        $t = Text::normalize($text);
        if (str_contains($t,'خرید'))     { $this->parent->transitionTo(StateKey::BuyChoosePlan->value); return; }
        if (str_contains($t,'پشتیبانی')) { $this->parent->transitionTo(StateKey::Support->value);      return; }
        if (str_contains($t,'مدیریت'))   { $this->parent->transitionTo(StateKey::ServersList->value);  return; }
        if (str_contains($t,'افزایش موجودی'))   { $this->parent->transitionTo(StateKey::WalletEnterAmount->value);  return; }
        $this->onEnter();
    }
}
