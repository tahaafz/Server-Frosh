<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class ChoosePlan extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $os = $this->getData('os','—');
        $kb = $this->inlineKeyboard([
            [
                ['text'=>'پلن 1GB','data'=>'plan:1gb'],
                ['text'=>'پلن 2GB','data'=>'plan:2gb'],
            ],
            [
                ['text'=>'⬅️ برگشت','data'=>'nav:back:choose_os'],
            ],
        ]);
        $this->edit("💡 پلن خود را انتخاب کنید:\nOS: <code>{$os}</code>", $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        if (str_starts_with($data,'plan:')) {
            $this->putData('plan', substr($data,5));
            $this->parent->transitionTo('enter_details'); return;
        }
        if ($data === 'nav:back:choose_os') {
            $this->parent->transitionTo('buy.choose_os'); return;
        }
        $this->onEnter();
    }
}
