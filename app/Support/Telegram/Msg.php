<?php

namespace App\Support\Telegram;

use Illuminate\Support\Facades\Lang;

final class Msg
{
    public static function resolve(string $textOrLangKey): string
    {
        if (str_contains($textOrLangKey, '.')) {
            $locale = app()->getLocale();

            if (Lang::has($textOrLangKey, $locale)) {
                return __($textOrLangKey);
            }
        }

        return $textOrLangKey;
    }
}
