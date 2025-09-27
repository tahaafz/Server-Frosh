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
            case 'خرید VPS':
                $this->newFlow();
                $this->putData('provider', $this->getData('provider', 'gcore'));
                $this->parent->transitionTo(StateKey::BuyChoosePlan->value);
                return true;

            case 'افزایش موجودی':
                $this->newFlow();
                $this->parent->transitionTo(StateKey::WalletEnterAmount->value);
                return true;

            case 'پشتیبانی':
                $this->parent->transitionTo(StateKey::Support->value);
                return true;

            case 'مدیریت سرورها':
                $this->parent->transitionTo(StateKey::ServersList->value);
                return true;

            default:
                return false;
        }
    }
}
