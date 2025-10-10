<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Callback\Action;
use App\Telegram\Core\DeclarativeState;
use App\Telegram\UI\{Btn, Row, InlineMenu};

class ChoosePlan extends DeclarativeState
{
    protected function screen(): array
    {
        $menu = InlineMenu::make(
            Row::make(
                Btn::key('telegram.plans.g2s_shared_1_1_25', Action::BuyPlan, ['code'=>'g2s-shared-1-1-25']),
                Btn::key('telegram.plans.g2s_shared_1_2_25', Action::BuyPlan, ['code'=>'g2s-shared-1-2-25'])
            )
        )->backTo('buy.provider');

        return ['text'=>'telegram.buy.choose_plan','menu'=>$menu];
    }

    protected function routes(): array
    {
        return [
            Action::BuyPlan->value => function(array $p){
                $this->putData('plan_code', (string)($p['code'] ?? ''));
                $this->goKey('buy.location');
            },
        ];
    }
}
