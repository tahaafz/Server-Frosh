<?php

namespace App\Telegram\UI;

use App\Models\TgState;
use App\Telegram\Callback\Action;

class RowBuilder
{
    /**
     * @param iterable<TgState> $buttons
     * @return Row[]
     */
    public static function build(iterable $buttons): array
    {
        $rows = [];
        $current = [];

        foreach ($buttons as $btn) {
            $tgBtn = Btn::key(
                $btn->title_key,
                Action::CatalogPick,
                ['id' => $btn->id]
            ); // متن دکمه از lang

            if ($btn->layout === 'below' || empty($current)) {
                if (!empty($current)) {
                    $rows[] = Row::make(...$current);
                    $current = [];
                }
                $current[] = $tgBtn;
            } else {
                $current[] = $tgBtn; // beside
            }
        }

        if (!empty($current)) {
            $rows[] = Row::make(...$current);
        }

        return $rows;
    }
}
