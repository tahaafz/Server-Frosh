<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Models\User;
use App\Services\Checkout\Calculator;
use App\Telegram\States\Support\CategoryDrivenState;

class Review extends CategoryDrivenState
{
    protected StateKey $stateKey = StateKey::BuyReview;
    protected string   $textKey  = 'telegram.buy.review';

    protected function enterEffects(User $user): array
    {
        $sum = (new Calculator())->summarize($user);
        return [
            'cart'    => number_format($sum['cart']),
            'balance' => number_format($sum['balance']),
            'deficit' => number_format($sum['deficit']),
        ];
    }
}
