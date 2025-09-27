<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Traits\Telegram\FlowToken;
use App\Traits\Telegram\MainMenuShortcuts;
use App\Traits\Telegram\PersistsData;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;

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

        if (str_starts_with($rest,'loc:')) {
            $this->putData('region_id', substr($rest,4));
            $this->parent->transitionTo(StateKey::BuyChooseOS->value);
            return;
        }
        if ($rest === 'back:plan') {
            $this->parent->transitionTo(StateKey::BuyChoosePlan->value);
            return;
        }
        $this->onEnter();
    }
}
