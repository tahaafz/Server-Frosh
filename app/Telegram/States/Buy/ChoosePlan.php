<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class ChoosePlan extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $this->send(
            "🔹 لطفاً پلن سرور خود را انتخاب کنید:\n\n" .
            "1. Plan 1 > RAM 1 | CPU 1 | Disk 25GB\n" .
            "2. Plan 2 > RAM 2 | CPU 1 | Disk 25GB",
            $this->inlineKeyboard([
                [
                    ['text' => 'Plan 1', 'data' => 'plan_1'],
                    ['text' => 'Plan 2', 'data' => 'plan_2'],
                ],
            ])
        );
    }

    public function onCallback(string $data, array $u): void
    {
        if ($data === 'plan_1') {
            $this->putData('plan', 'g2s-shared-1-1-25');
            $this->parent->transitionTo('choose_location');
        } elseif ($data === 'plan_2') {
            $this->putData('plan', 'g2s-shared-1-2-25');
            $this->parent->transitionTo('choose_location');
        }
    }
}
