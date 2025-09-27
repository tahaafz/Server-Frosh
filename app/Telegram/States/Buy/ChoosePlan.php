<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Traits\Telegram\FlowToken;
use App\Traits\Telegram\MainMenuShortcuts;
use App\Traits\Telegram\PersistsData;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;

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

        if (str_starts_with($rest,'plan:')) {
            $this->putData('plan', substr($rest,5));
            $this->parent->transitionTo(StateKey::BuyChooseLocation->value);
            return;
        }
        if ($rest === 'back:provider') {
            $this->parent->transitionTo(StateKey::BuyChooseProvider->value);
            return;
        }
        $this->onEnter();
    }
}
