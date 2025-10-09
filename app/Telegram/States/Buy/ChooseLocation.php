<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Callback\Action;
use App\Telegram\Core\AbstractState;
use App\Telegram\Nav\NavTarget;
use App\Telegram\UI\Buttons;
use App\Telegram\UI\ManagesScreens;

class ChooseLocation extends AbstractState
{
    use ManagesScreens;

    public function onEnter(): void
    {
        $inline = [
            [
                [
                    'text' => Buttons::label('locations.dubai', '🇦🇪 Dubai'),
                    'callback_data' => $this->cbBuild(Action::BuyLocation, ['id' => 116]),
                ],
                [
                    'text' => Buttons::label('locations.london', '🇬🇧 London'),
                    'callback_data' => $this->cbBuild(Action::BuyLocation, ['id' => 104]),
                ],
                [
                    'text' => Buttons::label('locations.frankfurt', '🇩🇪 Frankfurt'),
                    'callback_data' => $this->cbBuild(Action::BuyLocation, ['id' => 38]),
                ],
            ],
            [
                [
                    'text' => __('telegram.buttons.back'),
                    'callback_data' => $this->cbBuild(Action::NavBack, ['to' => NavTarget::Plan->value]),
                ],
            ],
        ];

        $this->ensureInlineScreen('telegram.buy.choose_location', ['inline_keyboard' => $inline]);
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
            case Action::BuyLocation:
                $this->putData('region_id', (string) ($params['id'] ?? ''));
                $this->goEnum(StateKey::BuyChooseOS);

                return;

            case Action::NavBack:
                $target = (string) ($params['to'] ?? '');

                if ($target === NavTarget::Plan->value) {
                    $this->goEnum(StateKey::BuyChoosePlan);

                    return;
                }

                if ($target === NavTarget::Welcome->value) {
                    $this->resetToWelcomeMenu();

                    return;
                }

                break;
        }

        $this->onEnter();
    }
}
