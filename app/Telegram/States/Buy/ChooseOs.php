<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Traits\Telegram\FlowToken;
use App\Traits\Telegram\MainMenuShortcuts;
use App\Traits\Telegram\PersistsData;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;

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

        if (str_starts_with($rest,'os:')) {
            $this->putData('os_image_id', substr($rest,3));
            $this->parent->transitionTo(StateKey::Confirm->value);
            return;
        }
        if ($rest === 'back:location') {
            $this->parent->transitionTo(StateKey::BuyChooseLocation->value);
            return;
        }
        $this->onEnter();
    }
}
