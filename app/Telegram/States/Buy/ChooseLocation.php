<?php

namespace App\Telegram\States\Buy;

class ChooseLocation extends \App\Telegram\Core\AbstractState
{

    public function onEnter(): void
    {
        $inlineKeyboard = [
            [
                ['text' => '🇦🇪 Dubai',     'callback_data' => $this->cbBuild(\App\Telegram\Callback\Action::BuyLocation, ['id' => 116])],
                ['text' => '🇬🇧 London',    'callback_data' => $this->cbBuild(\App\Telegram\Callback\Action::BuyLocation, ['id' => 104])],
                ['text' => '🇩🇪 Frankfurt', 'callback_data' => $this->cbBuild(\App\Telegram\Callback\Action::BuyLocation, ['id' => 38 ])],
            ],
            [
                ['text' => \App\Telegram\UI\Buttons::label('back'), 'callback_data' => $this->cbBackTo(\App\Telegram\Nav\NavTarget::Plan->value)],
            ],
        ];
        $this->editT('telegram.buy.choose_location', ['inline_keyboard' => $inlineKeyboard]);
    }

    public function onCallback(string $callbackData, array $update): void
    {
        $parsed = $this->cbParse($callbackData, $update);
        if (!$parsed) { $this->onEnter(); return; }

        switch ($parsed['action']) {
            case \App\Telegram\Callback\Action::BuyLocation:
                $selectedRegionId = (string)($parsed['params']['id'] ?? '');
                $this->putData('region_id', $selectedRegionId);
                $this->goEnum(\App\Enums\Telegram\StateKey::BuyChooseOS);
                return;
            case \App\Telegram\Callback\Action::NavBack:
                $targetKey = (string)($parsed['params']['to'] ?? '');
                if ($targetKey === \App\Telegram\Nav\NavTarget::Plan->value) {
                    $this->goEnum(\App\Enums\Telegram\StateKey::BuyChoosePlan);
                }
                return;
        }
        $this->onEnter();
    }
}
