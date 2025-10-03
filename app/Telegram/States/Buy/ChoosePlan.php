<?php

namespace App\Telegram\States\Buy;

class ChoosePlan extends \App\Telegram\Core\AbstractState
{

    public function onEnter(): void
    {
        $this->flow(); // ensure
        $rawKb = \App\Telegram\UI\KeyboardFactory::inlineBuyPlans('g2s-shared-1-1-25', 'g2s-shared-1-2-25');
        // pack flow token into every callback_data
        $kb = ['inline_keyboard' => array_map(function($row) {
            return array_map(function($btn) {
                if (isset($btn['callback_data'])) $btn['callback_data'] = $this->pack($btn['callback_data']);
                return $btn;
            }, $row);
        }, $rawKb['inline_keyboard'])];
        $this->sendT('telegram.buy.choose_plan', $kb);
    }

    public function onCallback(string $callbackData, array $update): void
    {
        $parsed = $this->cbParse($callbackData, $update);
        if (!$parsed) return;

        switch ($parsed['action']) {
            case \App\Telegram\Callback\Action::BuyPlan:
                $this->putData('plan', $parsed['params']['code'] ?? null);
                $this->goEnum(\App\Enums\Telegram\StateKey::BuyChooseLocation);
                return;
            case \App\Telegram\Callback\Action::NavBack:
                if (($parsed['params']['to'] ?? '') === \App\Telegram\Nav\NavTarget::Provider->value) {
                    $this->goEnum(\App\Enums\Telegram\StateKey::BuyChooseProvider);
                }
                return;
        }
        $this->onEnter();
    }
}
