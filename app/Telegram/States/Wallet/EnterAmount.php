<?php
namespace App\Telegram\States\Wallet;

use App\Enums\Telegram\StateKey;
use App\Support\Telegram\Text;
use App\Telegram\Core\State;
use App\Traits\Telegram\{ReadsUpdate,SendsMessages,PersistsData,MainMenuShortcuts,FlowToken};

class EnterAmount extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $this->newFlow();
        $this->send(__('telegram.wallet.enter_amount'));
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

        $this->parent->transitionTo(StateKey::WalletWaitReceipt->value);
    }
}
