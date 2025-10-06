<?php

namespace App\Traits\Telegram;

use App\Enums\Telegram\StateKey;

trait MainMenuShortcuts
{
    use FlowToken, PersistsData;

    protected function interceptShortcuts(?string $text): bool
    {
        if ($text === null) {
            return false;
        }

        $choice = trim($text);

        switch ($choice) {
            case \App\Telegram\UI\Buttons::label('buy'):
                $this->newFlow();
                $this->putData('provider', $this->getData('provider', 'gcore'));
                $this->parent->transitionTo(StateKey::BuyChoosePlan->value);
                return true;

            case \App\Telegram\UI\Buttons::label('topup'):
                $this->newFlow();
                $this->parent->transitionTo(StateKey::WalletEnterAmount->value);
                return true;

            case \App\Telegram\UI\Buttons::label('support'):
                $this->parent->transitionTo(StateKey::Support->value);
                return true;

            case \App\Telegram\UI\Buttons::label('manage'):
                $this->parent->transitionTo(StateKey::ServersList->value);
                return true;

            case \App\Telegram\UI\Buttons::label('management'):
                if (!$this->process()->is_admin) {
                    return false;
                }
                $this->parent->transitionTo(StateKey::AdminManagement->value);
                return true;

            default:
                return false;
        }
    }
}
