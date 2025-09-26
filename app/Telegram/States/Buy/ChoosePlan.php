<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\FlowToken;
use App\Telegram\Fsm\Traits\MainMenuShortcuts;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class ChoosePlan extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $this->flow(); // ensure
        $kb = $this->inlineKeyboard([
            [
                ['text'=>'Plan 1','data'=>$this->pack('plan:g2s-shared-1-1-25')],
                ['text'=>'Plan 2','data'=>$this->pack('plan:g2s-shared-1-2-25')],
            ],
            [
                ['text'=>'⬅️ برگشت','data'=>$this->pack('back:provider')],
            ],
        ]);
        $this->send("🔹 لطفاً پلن را انتخاب کنید:\n• 1GB RAM / 1 vCPU / 25GB\n• 2GB RAM / 1 vCPU / 25GB", $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;

        if (str_starts_with($rest,'plan:')) { $this->putData('plan', substr($rest,5)); $this->parent->transitionTo('buy.choose_location'); return; }
        if ($rest === 'back:provider')      { $this->parent->transitionTo('buy.choose_provider'); return; }

        $this->onEnter();
    }
}
