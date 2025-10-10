<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Callback\Action;
use App\Telegram\Core\DeclarativeState;
use App\Telegram\UI\{Btn, Row, InlineMenu};

class ChooseOs extends DeclarativeState
{
    protected function screen(): array
    {
        $menu = InlineMenu::make(
            Row::make(
                Btn::key('telegram.os.ubuntu22', Action::BuyOS, ['code'=>'ubuntu-22']),
                Btn::key('telegram.os.debian12', Action::BuyOS, ['code'=>'debian-12']),
            ),
        )->backTo('buy.location');

        return ['text'=>'telegram.buy.choose_os','menu'=>$menu];
    }

    protected function routes(): array
    {
        return [
            Action::BuyOS->value => function(array $params){
                $this->putData('os_code', (string)($params['code'] ?? 'ubuntu-22'));
                $this->goKey('buy.confirm');
            },
        ];
    }
}
