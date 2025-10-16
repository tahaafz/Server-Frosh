<?php

namespace App\Telegram\States;

use App\Enums\SupportTicketType;
use App\Models\SupportTicket;
use App\Services\Telegram\Admin\AdminMessenger;
use App\Telegram\Core\AbstractState;
use App\Telegram\UI\KeyboardFactory;

class Support extends AbstractState
{
    public function onEnter(): void
    {
        $this->expireInlineScreen();
        $this->sendWithReplyKeyboard('telegram.support.enter', KeyboardFactory::replyBackOnly());
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) return;

        $user = $this->process();

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'message' => $text,
            'type'    => SupportTicketType::Question,
        ]);

        app(AdminMessenger::class)->broadcastSupportFromUser($ticket);
        $this->sendWithReplyKeyboard('telegram.support.sent', KeyboardFactory::replyBackOnly());
    }
}
