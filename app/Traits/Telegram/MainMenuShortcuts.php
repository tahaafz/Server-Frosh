<?php

namespace App\Traits\Telegram;

use App\Enums\Telegram\StateKey;
use App\Support\Telegram\Text;
use App\Telegram\UI\Buttons;

trait MainMenuShortcuts
{
    use FlowToken, PersistsData;

    protected function interceptShortcuts(?string $text): bool
    {
        if ($text === null) {
            return false;
        }

        $choice = trim($text);
        $normalized = Text::normalize($choice);

        if ($normalized === null) {
            return false;
        }

        $labels = [
            'buy' => Text::normalize(Buttons::label('buy')),
            'topup' => Text::normalize(Buttons::label('topup')),
            'support' => Text::normalize(Buttons::label('support')),
            'manage' => Text::normalize(Buttons::label('manage')),
            'management' => Text::normalize(Buttons::label('management')),
            'back' => Text::normalize(Buttons::label('back')),
        ];

        switch (true) {
            case $normalized === $labels['buy'] || $normalized === 'buy':
                $this->newFlow();
                $this->putData('provider', $this->getData('provider', 'gcore'));
                $this->parent->transitionTo(StateKey::BuyChooseProvider->value);

                return true;

            case $normalized === $labels['topup'] || $normalized === 'topup':
                $this->newFlow();
                $this->parent->transitionTo(StateKey::WalletEnterAmount->value);

                return true;

            case $normalized === $labels['support'] || $normalized === 'support':
                $this->parent->transitionTo(StateKey::Support->value);

                return true;

            case $normalized === $labels['manage'] || $normalized === 'manage':
                $this->parent->transitionTo(StateKey::ServersList->value);

                return true;

            case $normalized === $labels['management'] || $normalized === 'management':
                if (! $this->process()->is_admin) {
                    return false;
                }
                $this->parent->transitionTo(StateKey::AdminManagement->value);

                return true;

            case $normalized === $labels['back'] || $normalized === 'back':
                $this->parent->transitionTo(StateKey::Welcome->value);

                return true;

            default:
                return false;
        }
    }
}
