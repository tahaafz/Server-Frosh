<?php

namespace App\Telegram\Fsm\Core;

final class Registry
{
    /** @return array<string,class-string<State>> */
    public static function map(): array
    {
        return [
            'welcome'         => \App\Telegram\States\Welcome::class,
            'buy.choose_provider' => \App\Telegram\States\Buy\ChooseProvider::class,
            'buy.choose_os'   => \App\Telegram\States\Buy\ChooseOs::class,
            'buy.choose_plan' => \App\Telegram\States\Buy\ChoosePlan::class,
            'buy.choose_location' => \App\Telegram\States\Buy\ChooseLocation::class,
            'enter_details'   => \App\Telegram\States\EnterDetails::class,
            'confirm'         => \App\Telegram\States\Confirm::class,
            'submit'          => \App\Telegram\States\Submit::class,
            'support'         => \App\Telegram\States\Support::class,
            'servers.list'     => \App\Telegram\States\Servers\ListServers::class,
            'servers.panel'    => \App\Telegram\States\Servers\ServerPanel::class,
        ];
    }
}
