<?php

namespace App\Telegram\Core;

use App\Enums\Telegram\StateKey;

class Registry
{
    public static function map(): array
    {
        return [
            StateKey::Welcome->value           => \App\Telegram\States\Welcome::class,
            StateKey::BuyChooseProvider->value => \App\Telegram\States\Buy\ChooseProvider::class,
            StateKey::BuyChoosePlan->value     => \App\Telegram\States\Buy\ChoosePlan::class,
            StateKey::BuyChooseLocation->value => \App\Telegram\States\Buy\ChooseLocation::class,
            StateKey::BuyChooseOS->value       => \App\Telegram\States\Buy\ChooseOS::class,
            StateKey::Confirm->value           => \App\Telegram\States\Confirm::class,
            StateKey::Support->value           => \App\Telegram\States\Support::class,
            StateKey::AdminManagement->value   => \App\Telegram\States\Admin\Management::class,
            StateKey::ServersList->value       => \App\Telegram\States\Servers\ListServers::class,
            StateKey::ServersPanel->value      => \App\Telegram\States\Servers\ServerPanel::class,
            StateKey::WalletEnterAmount->value => \App\Telegram\States\Wallet\EnterAmount::class,
            StateKey::WalletWaitReceipt->value => \App\Telegram\States\Wallet\WaitReceipt::class,
        ];
    }
}
