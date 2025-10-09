<?php

namespace App\Telegram\States;

use App\Telegram\UI\KeyboardFactory;

class Welcome extends \App\Telegram\Core\AbstractState
{
    public function onEnter(): void
    {
        $currentUser = $this->process();

        $this->send(
            __('telegram.welcome'),
            KeyboardFactory::replyMain($currentUser),
            trackMessage: false
        );
    }

    public function onText(string $text, array $u): void
    {
        $buy        = \App\Telegram\UI\Buttons::label('buy');
        $support    = \App\Telegram\UI\Buttons::label('support');
        $manage     = \App\Telegram\UI\Buttons::label('manage');
        $management = \App\Telegram\UI\Buttons::label('management');
        $topup      = \App\Telegram\UI\Buttons::label('topup');

        if ($text === $buy)     { $this->goEnum(\App\Enums\Telegram\StateKey::BuyChooseProvider); return; }
        if ($text === $support) { $this->goEnum(\App\Enums\Telegram\StateKey::Support);      return; }
        if ($text === $manage)  { $this->goEnum(\App\Enums\Telegram\StateKey::ServersList);  return; }
        if ($text === $management && $this->process()->is_admin) {
            $this->goEnum(\App\Enums\Telegram\StateKey::AdminManagement);
            return;
        }
        if ($text === $topup)   { $this->goEnum(\App\Enums\Telegram\StateKey::WalletEnterAmount);  return; }
        $this->onEnter();
    }

    protected function defaultReplyKeyboard(): array
    {
        $currentUser = $this->process();

        return KeyboardFactory::replyMain($currentUser);
    }
}
