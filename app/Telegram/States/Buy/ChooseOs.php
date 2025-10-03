<?php

namespace App\Telegram\States\Buy;

class ChooseOs extends \App\Telegram\Core\AbstractState
{

    public function onEnter(): void
    {
        $inlineKeyboard = [
            [
                [
                    'text' => \App\Telegram\UI\Buttons::label('os.ubuntu20', 'Ubuntu 20'),
                    'callback_data' => $this->cbBuild(\App\Telegram\Callback\Action::BuyOS, ['id' => 'ubuntu-20.04-x64'])
                ],
                [
                    'text' => \App\Telegram\UI\Buttons::label('os.ubuntu22', 'Ubuntu 22'),
                    'callback_data' => $this->cbBuild(\App\Telegram\Callback\Action::BuyOS, ['id' => 'ubuntu-22.04-x64'])
                ],
            ],
            [
                [
                    'text' => \App\Telegram\UI\Buttons::label('back'),
                    'callback_data' => $this->cbBackTo(\App\Telegram\Nav\NavTarget::Location->value)
                ],
            ],
        ];
        $this->editT('telegram.buy.choose_os', ['inline_keyboard' => $inlineKeyboard]);
    }

    public function onCallback(string $callbackData, array $update): void
    {
        $parsed = $this->cbParse($callbackData, $update);
        if (!$parsed) { $this->onEnter(); return; }

        switch ($parsed['action']) {
            case \App\Telegram\Callback\Action::BuyOS:
                $selectedOsId = (string)($parsed['params']['id'] ?? '');
                $this->putData('os_image_id', $selectedOsId);
                $this->goEnum(\App\Enums\Telegram\StateKey::Confirm);
                return;
            case \App\Telegram\Callback\Action::NavBack:
                $targetKey = (string)($parsed['params']['to'] ?? '');
                if ($targetKey === \App\Telegram\Nav\NavTarget::Location->value) {
                    $this->goEnum(\App\Enums\Telegram\StateKey::BuyChooseLocation);
                }
                return;
        }
        $this->onEnter();
    }
}
