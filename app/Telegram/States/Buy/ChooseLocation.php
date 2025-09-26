<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;
use App\Telegram\Fsm\Traits\FlowToken;
use App\Telegram\Fsm\Traits\MainMenuShortcuts;

class ChooseLocation extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $kb = $this->inlineKeyboard([
            [
                ['text'=>'🇦🇪 Dubai',     'data'=>$this->pack('loc:116')],
                ['text'=>'🇬🇧 London',    'data'=>$this->pack('loc:104')],
                ['text'=>'🇩🇪 Frankfurt', 'data'=>$this->pack('loc:38')],
            ],
            [
                ['text'=>'⬅️ برگشت','data'=>$this->pack('back:plan')],
            ],
        ]);
        $this->edit("📍 لطفاً لوکیشن را انتخاب کنید:", $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;

        if (str_starts_with($rest,'loc:')) { $this->putData('region_id', substr($rest,4)); $this->parent->transitionTo('buy.choose_os'); return; }
        if ($rest === 'back:plan')        { $this->parent->transitionTo('buy.choose_plan'); return; }

        $this->onEnter();
    }
}
