<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Telegram\Callback\{CallbackData, Action};
use App\Traits\Telegram\FlowToken;
use App\Traits\Telegram\MainMenuShortcuts;
use App\Traits\Telegram\PersistsData;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;
use App\Telegram\UI\Buttons;

class ChooseOs extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $rawKb = ['inline_keyboard' => [
            [
                [
                    'text'=>Buttons::label('os.ubuntu20', 'Ubuntu 20'),
                    'callback_data'=> CallbackData::build(Action::BuyOS, ['id'=>'ubuntu-20.04-x64'])
                ],
                [
                    'text'=>Buttons::label('os.ubuntu22', 'Ubuntu 22'),
                    'callback_data'=> CallbackData::build(Action::BuyOS, ['id'=>'ubuntu-22.04-x64'])
                ],
            ],
            [
                [
                    'text'=>Buttons::label('back'),
                    'callback_data'=> CallbackData::build(Action::NavBack, ['to'=>\App\Telegram\Nav\NavTarget::Location->value])
                ],
            ],
        ]];
        $kb = ['inline_keyboard' => array_map(function($row){
            return array_map(function($btn){
                if (isset($btn['callback_data'])) $btn['callback_data'] = $this->pack($btn['callback_data']);
                return $btn;
            }, $row);
        }, $rawKb['inline_keyboard'])];
        $this->edit(__('telegram.buy.choose_os'), $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;
        $parsed = CallbackData::parse($rest); if (!$parsed) return;

        switch ($parsed['action']) {
            case Action::BuyOS:
                $this->putData('os_image_id', (string)($parsed['params']['id'] ?? ''));
                $this->parent->transitionTo(StateKey::Confirm->value);
                return;
            case Action::NavBack:
                if (($parsed['params']['to'] ?? '') === \App\Telegram\Nav\NavTarget::Location->value) {
                    $this->parent->transitionTo(StateKey::BuyChooseLocation->value);
                }
                return;
        }
        $this->onEnter();
    }
}
