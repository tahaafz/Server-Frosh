<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class ChooseOs extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $kb = $this->inlineKeyboard([
            [
                ['text'=>'Ubuntu 22','data'=>'os:ubuntu-22'],
                ['text'=>'Debian 12','data'=>'os:debian-12'],
            ],
            [
                ['text'=>'⬅️ برگشت','data'=>'nav:back:welcome'],
            ],
        ]);
        $this->send("🚀 خرید VPS\nسیستم‌عامل را انتخاب کنید:", $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        if (str_starts_with($data,'os:')) {
            $this->putData('os', substr($data,3));
            $this->parent->transitionTo('buy.choose_plan'); return;
        }
        if ($data === 'nav:back:welcome') {
            $this->parent->transitionTo('welcome'); return;
        }
        $this->onEnter();
    }
}
