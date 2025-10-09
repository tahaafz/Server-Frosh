<?php
namespace App\Telegram\States\Wallet;

use App\Support\Telegram\Text;

class EnterAmount extends \App\Telegram\Core\AbstractState
{

    public function onEnter(): void
    {
        $this->newFlow();
        $this->sendT('telegram.wallet.enter_amount');
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) return;

        $amount = Text::parseAmountToman($text);
        if (!$amount || $amount < 50000) {
            $this->send(__('telegram.wallet.invalid_amount'));
            return;
        }
        $this->putData('topup_amount', $amount);
        $this->putData('topup_method', 'card');

        $this->goEnum(\App\Enums\Telegram\StateKey::WalletWaitReceipt);
    }

    protected function defaultReplyKeyboard(): ?array
    {
        return $this->backKeyboard();
    }
}
