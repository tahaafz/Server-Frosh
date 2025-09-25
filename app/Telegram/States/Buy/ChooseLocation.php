<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class ChooseLocation extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $this->send(
            "🇦🇪 لطفاً لوکیشن سرور خود را انتخاب کنید.",
            $this->inlineKeyboard([
                [
                    ['text' => 'Dubai', 'data' => 'location_dubai'],
                    ['text' => 'London', 'data' => 'location_london'],
                    ['text' => 'Frankfurt', 'data' => 'location_frankfurt'],
                ],
            ])
        );
    }

    public function onCallback(string $data, array $u): void
    {
        if ($data === 'location_dubai') {
            $this->putData('location', '116');
        } elseif ($data === 'location_london') {
            $this->putData('location', '104');
        } elseif ($data === 'location_frankfurt') {
            $this->putData('location', '38');
        }

        $this->parent->transitionTo('choose_os');
    }
}
