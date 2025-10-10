<?php

namespace App\Telegram\States\Buy;

use App\Telegram\Callback\Action;
use App\Telegram\Core\DeclarativeState;
use App\Telegram\UI\{Btn, Row, InlineMenu};

class ChooseProvider extends DeclarativeState
{
    protected function screen(): array
    {
        $menu = InlineMenu::make(
            Row::make(
                Btn::key('telegram.providers.gcore', Action::BuyPlan, ['provider'=>'gcore'])
            )
        );

        return [
            'text'         => 'telegram.buy.choose_provider',
            'menu'         => $menu,
            'reset_anchor' => true, // پیام اینلاین جدید؛ اما replyMain تغییری نمی‌کند
        ];
    }

    protected function routes(): array
    {
        return [
            Action::BuyPlan->value => function(array $p){
                $this->putData('provider', (string)($p['provider'] ?? 'gcore'));
                $this->goKey('buy.plan');
            },
        ];
    }
}
