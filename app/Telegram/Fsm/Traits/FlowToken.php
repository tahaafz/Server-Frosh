<?php

namespace App\Telegram\Fsm\Traits;

use Illuminate\Support\Str;
use Telegram\Bot\Laravel\Facades\Telegram;

trait FlowToken
{
    protected function newFlow(): string
    {
        $p = $this->process();
        $d = $p->tg_data ?? [];
        $d['flow_id'] = Str::upper(Str::random(6));
        unset($d['plan'], $d['region_id'], $d['os_image_id'], $d['vm_name'], $d['login_pass']);
        $d['provider'] = $d['provider'] ?? 'gcore';
        $p->tg_data = $d; $p->save();
        return $d['flow_id'];
    }

    protected function flow(): string
    {
        $p = $this->process();
        $d = $p->tg_data ?? [];
        return $d['flow_id'] ?? $this->newFlow();
    }

    protected function pack(string $payload): string
    {
        return 'f:'.$this->flow().'|'.$payload;
    }

    protected function invalidateUI(array $update, string $note = '⏱ این درخواست منقضی شده.'): void
    {
        if ($id = data_get($update, 'callback_query.id')) {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $id,
                'text'       => 'این درخواست منقضی شده',
                'show_alert' => false,
            ]);
        }

        $chatId    = data_get($update, 'callback_query.message.chat.id');
        $messageId = data_get($update, 'callback_query.message.message_id');
        $oldText   = data_get($update, 'callback_query.message.text');
        if ($chatId && $messageId && $oldText) {
            Telegram::editMessageText([
                'chat_id'                  => $chatId,
                'message_id'               => $messageId,
                'text'                     => $oldText . "\n\n".$note,
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => true,
                'reply_markup'             => ['inline_keyboard' => []], // حذف دکمه‌ها
            ]);
        }
    }

    protected function validateCallback(string $data, array $update): array
    {
        if (!preg_match('~^f:([A-Z0-9]{6})\|(.*)$~', $data, $m)) {
            $this->invalidateUI($update);
            return [false, null];
        }
        [$token, $rest] = [$m[1], $m[2]];
        $current = $this->flow();
        if ($token !== $current) {
            $this->invalidateUI($update);
            return [false, null];
        }
        return [true, $rest];
    }
}
