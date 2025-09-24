<?php

namespace App\Telegram\Fsm\Traits;

use Telegram\Bot\Laravel\Facades\Telegram;

trait SendsMessages
{
    protected function replyKeyboard(array $rows): array
    {
        return ['keyboard'=>$rows,'resize_keyboard'=>true,'one_time_keyboard'=>false];
    }

    protected function inlineKeyboard(array $rows): array
    {
        return [
            'inline_keyboard' => array_map(
                fn($r) => array_map(fn($b) => ['text'=>$b['text'],'callback_data'=>$b['data']], $r),
                $rows
            )
        ];
    }

    protected function send(string $text, ?array $replyMarkup = null): void
    {
        if (is_array($text)) {
            $text = json_encode($text);
        }

        $p = $this->process();
        $payload = [
            'chat_id' => $p->telegram_chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            $resp = Telegram::sendMessage($payload);
            if (isset($resp->messageId)) {
                $p->tg_last_message_id = $resp->messageId;
                $p->save();
            }
        } catch (\Exception $e) {
            \Log::error('Telegram send message error: ' . $e->getMessage(), [
                'payload' => $payload,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    protected function edit(string $text, ?array $replyMarkup = null): void
    {
        if (is_array($text)) {
            $text = json_encode($text);
        }

        $p = $this->process();
        if (!$p->tg_last_message_id) { $this->send($text, $replyMarkup); return; }

        $payload = [
            'chat_id' => $p->telegram_chat_id,
            'message_id' => $p->tg_last_message_id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];
        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            Telegram::editMessageText($payload);
        } catch (\Exception $e) {
            \Log::error('Telegram edit message error: ' . $e->getMessage(), [
                'payload' => $payload,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
}
