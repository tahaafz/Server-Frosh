<?php

namespace App\Telegram\Core;

use App\Enums\Telegram\StateKey;
use App\Telegram\Callback\{Action, CallbackData};
use App\Telegram\UI\KeyboardFactory;
use App\Traits\Telegram\{ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken};
use Illuminate\Support\Facades\Lang;

abstract class AbstractState extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    protected function sendT(string $textOrLangKey, ?array $replyMarkup = null, string $parseMode = 'HTML'): void
    {
        $text = $this->resolveText($textOrLangKey);
        $this->send($text, $replyMarkup);
    }

    protected function editT(string $textOrLangKey, ?array $replyMarkup = null, string $parseMode = 'HTML'): void
    {
        $text = $this->resolveText($textOrLangKey);
        $this->edit($text, $replyMarkup);
    }

    protected function resolveText(string $textOrLangKey): string
    {
        if (str_contains($textOrLangKey, '.')) {
            $locale = app()->getLocale();
            if (Lang::has($textOrLangKey, $locale)) return __($textOrLangKey);
        }
        return $textOrLangKey;
    }

    protected function goEnum(StateKey $stateKey): void
    {
        $this->parent->transitionTo($stateKey->value);
    }

    protected function goKey(string $stateKey): void
    {
        $this->parent->transitionTo($stateKey);
    }

    protected function cbBuild(Action $action, array $params = []): string
    {
        $raw = CallbackData::build($action, $params);
        return $this->pack($raw);
    }

    protected function cbParse(string $packedCallbackData, array $update): ?array
    {
        [$ok, $raw] = $this->validateCallback($packedCallbackData, $update);
        if (!$ok || $raw === null) return null;
        return CallbackData::parse($raw);
    }

    protected function cbBackTo(string $targetKey): string
    {
        return $this->cbBuild(Action::NavBack, ['to' => $targetKey]);
    }

    /** Main reply keyboard */
    protected function mainMenuKeyboard(): array
    {
        return KeyboardFactory::replyMain();
    }
}

