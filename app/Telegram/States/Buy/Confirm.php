<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Callback\Action;
use App\Telegram\Core\DeclarativeState;
use App\Telegram\UI\{Btn, Row, InlineMenu};

class Confirm extends DeclarativeState
{
    protected function screen(): array
    {
        $provider = $this->getData('provider','gcore');
        $plan     = $this->getData('plan_code','');
        $region   = $this->getData('region_id','');
        $os       = $this->getData('os_code','');

        $text = __('telegram.buy.confirm', compact('provider','plan','region','os'));

        $menu = InlineMenu::make(
            Row::make( Btn::key('telegram.buttons.confirm', Action::BuyConfirm) )
        )->backTo('buy.os');

        return ['text'=>$text,'menu'=>$menu];
    }

    protected function routes(): array
    {
        return [
            Action::BuyConfirm->value => fn()=> $this->goKey('buy.submit'),
        ];
    }
}
