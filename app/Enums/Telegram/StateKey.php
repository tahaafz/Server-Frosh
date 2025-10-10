<?php

namespace App\Enums\Telegram;

enum StateKey: string
{
    case Welcome           = 'welcome';
    case Support           = 'support';

    case BuyChooseProvider = 'buy.provider';
    case BuyChoosePlan     = 'buy.plan';
    case BuyChooseLocation = 'buy.location';
    case BuyChooseOS       = 'buy.os';
    case BuyConfirm        = 'buy.confirm';
    case BuySubmit         = 'buy.submit';

    case ServersList       = 'servers.list';

    case WalletEnterAmount = 'wallet.enter_amount';
    case WalletWaitReceipt = 'wallet.wait_receipt';
}
