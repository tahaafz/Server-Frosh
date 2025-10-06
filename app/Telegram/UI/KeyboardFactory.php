<?php

namespace App\Telegram\UI;

use App\Models\User;
use App\Telegram\Callback\{Action, CallbackData};
use App\Telegram\UI\Buttons;

final class KeyboardFactory
{
    public static function replyMain(?User $user = null): array
    {
        $keyboard = [[
            Buttons::label('buy'),
            Buttons::label('support'),
            Buttons::label('manage'),
        ]];

        $secondaryRow = [Buttons::label('topup')];

        if ($user?->is_admin) {
            $secondaryRow[] = Buttons::label('management');
        }

        $keyboard[] = $secondaryRow;

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
    }

    public static function inlineBackTo(string $target): array
    {
        return ['inline_keyboard' => [[[
            'text' => Buttons::label('back'),
            'callback_data' => CallbackData::build(Action::NavBack, ['to' => $target]),
        ]]]];
    }

    public static function inlineBuyPlans(string $p1, string $p2): array
    {
        return ['inline_keyboard' => [
            [[
                'text' => Buttons::label('buy.plan1', 'Plan 1'),
                'callback_data' => CallbackData::build(Action::BuyPlan, ['code' => $p1]),
            ], [
                'text' => Buttons::label('buy.plan2', 'Plan 2'),
                'callback_data' => CallbackData::build(Action::BuyPlan, ['code' => $p2]),
            ]],
            [[
                'text' => Buttons::label('back'),
                'callback_data' => CallbackData::build(Action::NavBack, ['to' => \App\Telegram\Nav\NavTarget::Provider->value]),
            ]],
        ]];
    }

    public static function inlineTopupModeration(int $reqId): array
    {
        return ['inline_keyboard' => [[
            [
                'text' => Buttons::label('approve'),
                'callback_data' => CallbackData::build(Action::TopupApprove, ['id' => $reqId]),
            ], [
                'text' => Buttons::label('reject'),
                'callback_data' => CallbackData::build(Action::TopupReject, ['id' => $reqId]),
            ],
        ]]];
    }
}
