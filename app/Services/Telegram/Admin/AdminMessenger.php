<?php

namespace App\Services\Telegram\Admin;

use App\Models\SupportTicket;
use App\Models\TopupRequest;
use App\Models\User;
use App\Traits\Telegram\TgApi;
use App\Telegram\UI\Buttons;

class AdminMessenger
{
    use TgApi;

    public function broadcastTopupRequest(TopupRequest $req): void
    {
        $cap = __('telegram.topup.request_title')."\n"
            . "UserID: <code>{$req->user_id}</code> • "
            . __('telegram.topup.line_amount', ['amount' => number_format($req->amount)])."\n"
            . "Method: <code>{$req->method}</code> • " . __('telegram.topup.line_id', ['id' => $req->id]);

        $kb = [
            'inline_keyboard' => [[
                [
                    'text' => \App\Telegram\UI\Buttons::label('approve'),
                    'callback_data' => \App\Telegram\Callback\CallbackData::build(\App\Telegram\Callback\Action::TopupApprove, ['id' => $req->id]),
                ], [
                    'text' => \App\Telegram\UI\Buttons::label('reject'),
                    'callback_data' => \App\Telegram\Callback\CallbackData::build(\App\Telegram\Callback\Action::TopupReject, ['id' => $req->id]),
                ],
            ]],
        ];

        User::query()->where('is_admin',true)->whereNotNull('telegram_chat_id')->chunkById(200, function($admins) use ($req,$cap,$kb) {
            foreach ($admins as $a) {
                if ($req->receipt_file_id) {
                    $this->tgSendPhoto($a->telegram_chat_id, $req->receipt_file_id, $cap, $kb);
                } else {
                    $this->tgSend($a->telegram_chat_id, $cap, $kb);
                }
            }
        });
    }

    public function broadcastSupportFromUser(SupportTicket $ticket, ?string $photoFileId = null): void
    {
        $ticket->loadMissing('user');
        $from = $ticket->user;

        if (!$from) {
            return;
        }

        $cap = __('telegram.admin.support_from_user_title')."\n"
            . "Ticket: <code>{$ticket->id}</code> • User: <code>{$from->id}</code> • TG: <code>{$from->telegram_user_id}</code>\n\n"
            . $ticket->message;

        $kb = [
            'inline_keyboard' => [[[
                'text' => Buttons::label('reply'),
                'callback_data' => \App\Telegram\Callback\CallbackData::build(\App\Telegram\Callback\Action::AdminReplyStart, [
                    'user' => $from->id,
                    'ticket' => $ticket->id,
                ]),
            ]]],
        ];

        User::query()
            ->where('is_admin', true)
            ->whereNotNull('telegram_chat_id')
            ->chunkById(200, function ($admins) use ($cap, $kb, $photoFileId) {
                foreach ($admins as $a) {
                    if ($photoFileId) {
                        $this->tgSendPhoto($a->telegram_chat_id, $photoFileId, $cap, $kb);
                    } else {
                        $this->tgSend($a->telegram_chat_id, $cap, $kb);
                    }
                }
            });
    }

    public function notifyAll(string $html): void
    {
        User::query()->where('is_admin',true)->whereNotNull('telegram_chat_id')->chunkById(200, function($admins) use ($html) {
            foreach ($admins as $a) $this->tgSend($a->telegram_chat_id, $html);
        });
    }
}
