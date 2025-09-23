<?php

namespace App\Telegram\States;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class Confirm extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $txt = "🧾 خلاصه سفارش:\n"
            . "• OS: <code>".$this->getData('os','—')."</code>\n"
            . "• Plan: <code>".$this->getData('plan','—')."</code>\n"
            . "• نام: <code>".$this->getData('name','—')."</code>\n"
            . "• آدرس: <code>".$this->getData('address','—')."</code>";

        $kb = $this->inlineKeyboard([
            [ ['text'=>'✅ تأیید نهایی','data'=>'confirm:yes'] ],
            [ ['text'=>'✏️ ویرایش مشخصات','data'=>'nav:details'], ['text'=>'⬅️ برگشت پلن','data'=>'nav:plan'] ],
        ]);

        $this->edit($txt, $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        if ($data === 'confirm:yes') { $this->parent->transitionTo('submit'); return; }
        if ($data === 'nav:details') {
            $this->putData('_details_stage','ask_name');
            $this->parent->transitionTo('enter_details'); return;
        }
        if ($data === 'nav:plan') { $this->parent->transitionTo('buy.choose_plan'); return; }
        $this->onEnter();
    }
}
