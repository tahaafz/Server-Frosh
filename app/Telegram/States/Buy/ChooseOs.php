<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class ChooseOS extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $this->send(
            "🚀 لطفاً سیستم‌عامل سرور خود را انتخاب کنید.",
            $this->inlineKeyboard([
                [
                    ['text' => 'Ubuntu 20', 'data' => 'os_ubuntu_20'],
                    ['text' => 'Ubuntu 22', 'data' => 'os_ubuntu_22'],
                ],
            ])
        );
    }

    public function onCallback(string $data, array $u): void
    {
        if ($data === 'os_ubuntu_20') {
            $this->putData('os', 'ubuntu-20.04-x64');
        } elseif ($data === 'os_ubuntu_22') {
            $this->putData('os', 'ubuntu-22.04-x64');
        }

        $this->parent->transitionTo('confirm');
    }
}
