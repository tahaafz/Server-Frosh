<?php

namespace App\Telegram\Fsm\Traits;

trait ReadsUpdate
{
    protected function extract(array $u): array
    {
        $text = $u['message']['text'] ?? null;
        if ($text !== null) {
            $t = mb_strtolower($text,'UTF-8');
            $t = preg_replace('/[^\p{L}\p{N}\s]/u','',$t);
            $text = trim($t);
        }
        $cbData = $u['callback_query']['data'] ?? null;
        return [$text, $cbData];
    }
}
