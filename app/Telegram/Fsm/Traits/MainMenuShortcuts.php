<?php

namespace App\Telegram\Fsm\Traits;

trait MainMenuShortcuts
{
    use FlowToken, PersistsData;

    protected function interceptShortcuts(?string $text): bool
    {
        if ($text === null) return false;

        if ($text === 'خرید vps' || str_contains($text,'خرید') || str_contains($text,'vps')) {
            $this->newFlow();
            $this->putData('provider', $this->getData('provider','gcore'));
            $this->parent->transitionTo('buy.choose_plan');
            return true;
        }

        if ($text === 'پشتیبانی' || str_contains($text,'support')) {
            $this->parent->transitionTo('support'); return true;
        }
        if (str_contains($text,'مدیریت')) {
            $this->parent->transitionTo('servers.list'); return true;
        }
        return false;
    }
}
