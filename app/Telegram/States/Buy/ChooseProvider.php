<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\{ReadsUpdate,SendsMessages,PersistsData,MainMenuShortcuts,FlowToken};

class ChooseProvider extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $kb = $this->inlineKeyboard([
            [ ['text'=>'GCore','data'=>$this->pack('prov:gcore')] ],
            [ ['text'=>'⬅️ بازگشت','data'=>$this->pack('back:welcome')] ],
        ]);
        $this->send("🔰 ارائه‌دهنده را انتخاب کنید:", $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;

        if ($rest === 'prov:gcore') { $this->putData('provider','gcore'); $this->parent->transitionTo('buy.choose_plan'); return; }
        if ($rest === 'back:welcome') { $this->parent->transitionTo('welcome'); return; }

        $this->onEnter();
    }
}
