<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Callback\Action;
use App\Telegram\Core\AbstractState;
use App\Telegram\Nav\NavTarget;
use App\Telegram\UI\Buttons;
use App\Telegram\UI\ManagesScreens;

class ChoosePlan extends AbstractState
{
    use ManagesScreens;

    public function onEnter(): void
    {
        $inline = [
            [
                [
                    'text' => Buttons::label('buy.plan1', 'Plan 1'),
                    'callback_data' => $this->cbBuild(Action::BuyPlan, ['code' => 'g2s-shared-1-1-25']),
                ],
                [
                    'text' => Buttons::label('buy.plan2', 'Plan 2'),
                    'callback_data' => $this->cbBuild(Action::BuyPlan, ['code' => 'g2s-shared-1-2-25']),
                ],
            ],
            [
                [
                    'text' => __('telegram.buttons.back'),
                    'callback_data' => $this->cbBuild(Action::NavBack, ['to' => NavTarget::Provider->value]),
                ],
            ],
        ];

        $this->ensureInlineScreen('telegram.buy.choose_plan', ['inline_keyboard' => $inline]);
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
                $this->putData('plan_code', (string) ($params['code'] ?? ''));
                $this->goEnum(StateKey::BuyChooseLocation);

                return;

            case Action::NavBack:
                $target = (string) ($params['to'] ?? '');

                if ($target === NavTarget::Provider->value) {
                    $this->goEnum(StateKey::BuyChooseProvider);

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
