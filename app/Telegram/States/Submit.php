<?php

namespace App\Telegram\States;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class Submit extends State
{
    use SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $orderId = 'ORD-'.now()->format('YmdHis');
        $this->putData('order_id', $orderId);

        $this->send("🎉 سفارش ثبت شد!\nشماره سفارش: <code>{$orderId}</code>\n\nبرای شروع: /start");

        // اگر می‌خوای پروسه ریست شود:
        // $p = $this->process();
        // $p->tg_current_state = null; $p->tg_data = null; $p->tg_last_message_id = null; $p->save();
    }
}
