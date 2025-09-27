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
        $this->send("💰 لطفاً مبلغ شارژ را به <b>تومان</b> وارد کنید (فقط عدد).");
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) return;

        $amount = Text::parseAmountToman($text);
        if (!$amount || $amount < 50000) {
            $this->send("❗️ مبلغ معتبر نیست. یک عدد (حداقل ۵۰,۰۰۰ تومان) ارسال کنید.");
            return;
        }
        $this->putData('topup_amount', $amount);
        $this->putData('topup_method', 'card');

        $this->parent->transitionTo(StateKey::WalletWaitReceipt->value);
    }
}
