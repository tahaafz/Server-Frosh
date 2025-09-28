<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Telegram\UI\KeyboardFactory as KB;
use App\Telegram\Callback\{CallbackData, Action};
use App\Traits\Telegram\FlowToken;
use App\Traits\Telegram\MainMenuShortcuts;
use App\Traits\Telegram\PersistsData;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;

class ChoosePlan extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $this->flow(); // ensure
        $rawKb = KB::inlineBuyPlans('g2s-shared-1-1-25', 'g2s-shared-1-2-25');
        // pack flow token into every callback_data
        $kb = ['inline_keyboard' => array_map(function($row) {
            return array_map(function($btn) {
                if (isset($btn['callback_data'])) $btn['callback_data'] = $this->pack($btn['callback_data']);
                return $btn;
            }, $row);
        }, $rawKb['inline_keyboard'])];
        $this->send(__('telegram.buy.choose_plan'), $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;
        $parsed = CallbackData::parse($rest); if (!$parsed) return;

        switch ($parsed['action']) {
            case Action::BuyPlan:
                $this->putData('plan', $parsed['params']['code'] ?? null);
                $this->parent->transitionTo(StateKey::BuyChooseLocation->value);
                return;
            case Action::NavBack:
                if (($parsed['params']['to'] ?? '') === \App\Telegram\Nav\NavTarget::Provider->value) {
                    $this->parent->transitionTo(StateKey::BuyChooseProvider->value);
                }
                return;
        }
        $this->onEnter();
    }
}
