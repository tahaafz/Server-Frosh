<?php

namespace App\Telegram\States\Buy;

class ChooseProvider extends \App\Telegram\Core\AbstractState
{

    public function onEnter(): void
    {
        $kb = $this->inlineKeyboard([
            [ ['text'=>\App\Telegram\UI\Buttons::label('provider.gcore', 'GCore'),'data'=>$this->pack('prov:gcore')] ],
            [ ['text'=>\App\Telegram\UI\Buttons::label('back'),'data'=>$this->pack('back:welcome')] ],
        ]);
        $this->sendT('telegram.buy.choose_provider', $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;

        if ($rest === 'prov:gcore') { $this->putData('provider','gcore'); $this->goEnum(\App\Enums\Telegram\StateKey::BuyChoosePlan); return; }
        if ($rest === 'back:welcome') { $this->goEnum(\App\Enums\Telegram\StateKey::Welcome); return; }

        $this->onEnter();
    }
}
