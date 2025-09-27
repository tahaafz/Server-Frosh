<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Traits\{Telegram\ReadsUpdate};
use App\Traits\Telegram\FlowToken;
use App\Traits\Telegram\MainMenuShortcuts;
use App\Traits\Telegram\PersistsData;
use App\Traits\Telegram\SendsMessages;

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

        if ($rest === 'prov:gcore') { $this->putData('provider','gcore'); $this->parent->transitionTo(StateKey::BuyChoosePlan->value); return; }
        if ($rest === 'back:welcome') { $this->parent->transitionTo(StateKey::Welcome->value); return; }

        $this->onEnter();
    }
}
