<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Callback\Action;
use App\Telegram\Core\AbstractState;
use App\Telegram\Nav\NavTarget;
use App\Telegram\UI\Buttons;
use App\Telegram\UI\ManagesScreens;

class ChooseProvider extends AbstractState
{
    use ManagesScreens;

    public function onEnter(): void
    {
        $this->hideReplyKeyboardOnce();

        $inline = [
            [
                [
                    'text' => Buttons::label('provider.gcore', 'Gcore'),
                    'callback_data' => $this->cbBuild(Action::BuyPlan, ['provider' => 'gcore']),
                ],
            ],
            [
                [
                    'text' => __('telegram.buttons.back'),
                    'callback_data' => $this->cbBuild(Action::NavBack, ['to' => NavTarget::Welcome->value]),
                ],
            ],
        ];

        $this->ensureInlineScreen('telegram.buy.choose_provider', ['inline_keyboard' => $inline], resetAnchor: true);
    }

    public function onCallback(string $callbackData, array $update): void
    {
        $parsed = $this->cbParse($callbackData, $update);
        if (! $parsed) {
            $this->onEnter();

            return;
        }

        $action = $parsed['action'];
        $params = $parsed['params'];

        switch ($action) {
            case Action::BuyPlan:
                $this->putData('provider', (string) ($params['provider'] ?? 'gcore'));
                $this->goEnum(StateKey::BuyChoosePlan);

                return;

            case Action::NavBack:
                if (($params['to'] ?? '') === NavTarget::Welcome->value) {
                    $this->resetToWelcomeMenu();

                    return;
                }

                break;
        }

        $this->onEnter();
    }
}
