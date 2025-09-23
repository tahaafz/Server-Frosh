<?php

namespace App\Telegram\States;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;

class EnterDetails extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $stage = $this->getData('_details_stage','ask_name');

        if ($stage === 'ask_name') { $this->send("👤 لطفاً نام و نام‌خانوادگی خود را بنویسید:"); return; }
        if ($stage === 'ask_addr') { $this->send("🏠 لطفاً آدرس دقیق ارسال را بنویسید:");       return; }

        $this->parent->transitionTo('confirm');
    }

    public function onText(string $text, array $u): void
    {
        $stage = $this->getData('_details_stage','ask_name');

        if ($stage === 'ask_name') {
            if (mb_strlen($text) < 2) { $this->send("✍️ نام معتبر نیست. دوباره وارد کنید:"); return; }
            $this->putData('name', $text);
            $this->putData('_details_stage','ask_addr');
            $this->onEnter(); return;
        }

        if ($stage === 'ask_addr') {
            if (mb_strlen($text) < 5) { $this->send("📦 آدرس خیلی کوتاه است. لطفاً کامل‌تر بنویسید:"); return; }
            $this->putData('address', $text);
            $this->putData('_details_stage','done');
            $this->parent->transitionTo('confirm'); return;
        }

        $this->putData('_details_stage','ask_name');
        $this->onEnter();
    }
}
