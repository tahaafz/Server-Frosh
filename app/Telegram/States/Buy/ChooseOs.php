<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Callback\Action;
use App\Telegram\Core\AbstractState;
use App\Telegram\Nav\NavTarget;
use App\Telegram\UI\Buttons;
use App\Telegram\UI\ManagesScreens;

class ChooseOs extends AbstractState
{
    use ManagesScreens;

    public function onEnter(): void
    {
        $inline = [
            [
                [
                    'text' => Buttons::label('os.ubuntu20', 'Ubuntu 20'),
                    'callback_data' => $this->cbBuild(Action::BuyOS, ['id' => 'ubuntu-20.04-x64']),
                ],
                [
                    'text' => Buttons::label('os.ubuntu22', 'Ubuntu 22'),
                    'callback_data' => $this->cbBuild(Action::BuyOS, ['id' => 'ubuntu-22.04-x64']),
                ],
            ],
            [
                [
                    'text' => __('telegram.buttons.back'),
                    'callback_data' => $this->cbBuild(Action::NavBack, ['to' => NavTarget::Location->value]),
                ],
            ],
        ];

        $this->ensureInlineScreen('telegram.buy.choose_os', ['inline_keyboard' => $inline]);
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
            case Action::BuyOS:
                $this->putData('os_image_id', (string) ($params['id'] ?? ''));
                $this->goEnum(StateKey::Confirm);

                return;

            case Action::NavBack:
                $target = (string) ($params['to'] ?? '');

                if ($target === NavTarget::Location->value) {
                    $this->goEnum(StateKey::BuyChooseLocation);

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
