<?php

namespace App\Telegram\States\Buy;

use App\Enums\Telegram\StateKey;
use App\Services\Calculator;
use App\Telegram\Callback\Action;
use App\Telegram\Core\AbstractState;
use App\Telegram\Core\DeclarativeState;
use App\Telegram\UI\{Btn, Row, InlineMenu};
use App\Services\Cart\UserCart;

class Confirm extends AbstractState
{
    public function onEnter(): void
    {
        $user = $this->process();
        $sum  = (new Calculator())->summarize($user);

        if ($sum['sufficient']) {
            $user->balance   -= (int) $user->cart_total;
            $user->cart_total = 0;
            $user->save();

            $this->goKey(StateKey::BuySubmit->value);
            return;
        }

        $menu = InlineMenu::make(
            Row::make(Btn::key('telegram.buttons.increase_balance', Action::CheckoutTopup->value))
        )->backTo(Action::Back->value, 'telegram.buttons.back');

        $this->sayKey('telegram.buy.checkout.insufficient', menu: $menu, vars: [
            'cart'    => number_format($sum['cart']),
            'balance' => number_format($sum['balance']),
            'deficit' => number_format($sum['deficit']),
        ]);
    }

    public function onText(?string $text, array $u): void
    {
        $payload = trim((string) $text);

        if ($payload === Action::CheckoutTopup->value) {
            $this->goKey(StateKey::WalletWaitReceipt->value);
            return;
        }

        if ($payload === Action::Back->value) {
            $this->goKey(StateKey::BuyReview->value);
            return;
        }

        $this->silent();
    }
}
