<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\FlowToken;
use App\Telegram\Fsm\Traits\MainMenuShortcuts;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class ChooseOs extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $kb = $this->inlineKeyboard([
            [
                ['text'=>'Ubuntu 20','data'=>$this->pack('os:ubuntu-20.04-x64')],
                ['text'=>'Ubuntu 22','data'=>$this->pack('os:ubuntu-22.04-x64')],
            ],
            [
                ['text'=>'⬅️ برگشت','data'=>$this->pack('back:location')],
            ],
        ]);
        $this->edit("🖥 نسخهٔ سیستم‌عامل را انتخاب کنید:", $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;

        if (str_starts_with($rest,'os:')) { $this->putData('os_image_id', substr($rest,3)); $this->parent->transitionTo('confirm'); return; }
        if ($rest === 'back:location')    { $this->parent->transitionTo('buy.choose_location'); return; }

        $this->onEnter();
    }
}
