<?php

namespace App\Telegram\Callback;

enum Action: string
{
    case NavBack         = 'nav.back';
    case BuyPlan         = 'buy.plan';
    case BuyLocation     = 'buy.location';
    case BuyOS           = 'buy.os';
    case ServersPanel    = 'srv.panel';
    case ServerAction    = 'srv.act';
    case TopupCancel     = 'topup.cancel';
    case TopupApprove    = 'topup.approve';
    case TopupReject     = 'topup.reject';
    case AdminReplyStart = 'admin.reply.start';
}

