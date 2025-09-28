<?php

namespace App\Telegram\States;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Telegram\UI\KeyboardFactory as KB;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;
use APP\Support\Telegram\Text;

class Welcome extends State
{
    use ReadsUpdate, SendsMessages;

    public function onEnter(): void
    {
        $this->send(__('telegram.welcome.intro'), KB::replyMain());
    }

    public function onText(string $text, array $u): void
    {
        $buy     = \App\Telegram\UI\Buttons::label('buy');
        $support = \App\Telegram\UI\Buttons::label('support');
        $manage  = \App\Telegram\UI\Buttons::label('manage');
        $topup   = \App\Telegram\UI\Buttons::label('topup');

        if ($text === $buy)     { $this->parent->transitionTo(StateKey::BuyChoosePlan->value); return; }
        if ($text === $support) { $this->parent->transitionTo(StateKey::Support->value);      return; }
        if ($text === $manage)  { $this->parent->transitionTo(StateKey::ServersList->value);  return; }
        if ($text === $topup)   { $this->parent->transitionTo(StateKey::WalletEnterAmount->value);  return; }
        $this->onEnter();
    }
}
