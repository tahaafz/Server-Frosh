<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Callback\Action;
use App\Telegram\Core\DeclarativeState;
use App\Telegram\UI\{Btn, Row, InlineMenu};

class ChooseLocation extends DeclarativeState
{
    protected function screen(): array
    {
        $menu = InlineMenu::make(
            Row::make(
                Btn::key('telegram.regions.dubai',     Action::BuyLocation, ['id'=>116]),
                Btn::key('telegram.regions.london',    Action::BuyLocation, ['id'=>104]),
                Btn::key('telegram.regions.frankfurt', Action::BuyLocation, ['id'=>38 ]),
            ),
        )->backTo('buy.plan');

        return ['text'=>'telegram.buy.choose_location','menu'=>$menu];
    }

    protected function routes(): array
    {
        return [
            Action::BuyLocation->value => function(array $params){
                $this->putData('region_id', (string)($params['id'] ?? ''));
                $this->goKey('buy.os');
            },
        ];
    }
}
