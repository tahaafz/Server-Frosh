<?php

namespace App\Enums\Telegram;

enum StateKey: string
{
    case Welcome          = 'welcome';
    case BuyChooseProvider = 'buy.choose_provider';
    case BuyChoosePlan     = 'buy.choose_plan';
    case BuyChooseLocation = 'buy.choose_location';
    case BuyChooseOS       = 'buy.choose_os';
    case Confirm           = 'confirm';
    case Support          = 'support';
    case AdminManagement  = 'admin.management';
    case ServersList      = 'servers.list';
    case ServersPanel     = 'servers.panel';
    case WalletEnterAmount  = 'wallet.amount';
    case WalletWaitReceipt  = 'wallet.receipt';
}
