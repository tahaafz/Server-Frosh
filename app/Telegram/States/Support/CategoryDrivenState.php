<?php

namespace App\Telegram\States\Support;

use App\Enums\Telegram\StateKey;
use App\Models\{Category, CategoryState, User};
use App\Services\Cart\UserCart;
use App\Telegram\Callback\Action;
use App\Telegram\Core\DeclarativeState;
use App\Telegram\UI\InlineMenu;
use App\Telegram\UI\RowBuilder;
use Illuminate\Support\Facades\DB;

abstract class CategoryDrivenState extends DeclarativeState
{
    protected StateKey $stateKey;

    protected string $textKey;

    protected bool $resetCartOnEnter = false;

    protected function screen(): array
    {
        return [];
    }

    protected function routes(): array
    {
        return [];
    }

    protected function enterEffects(User $user): array
    {
        return [];
    }

    public function onEnter(): void
    {
        $user = $this->process();

        if ($this->resetCartOnEnter) {
            UserCart::reset($user);
        }

        $slug     = $this->stateKey->categorySlug(); // از enum
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        $buttons  = $category->buttons()->get();

        $rows = RowBuilder::build($buttons);
        $menu = InlineMenu::make(...$rows);

        if ($this->stateKey->back()) {
            $menu->backTo(Action::Back->value, 'telegram.buttons.back');
        }

        $vars = (array) $this->enterEffects($user);

        $this->sayKey($this->textKey, menu: $menu, vars: $vars);
    }

    public function onText(string $text, array $u): void
    {
        if ($this->interceptShortcuts($text)) {
            return;
        }

        $payload = trim($text);

        if ($payload === Action::Back->value && ($prev = $this->stateKey->back())) {
            $this->handleBack($prev);
            return;
        }

        $prefix = Action::CatalogPick->value . ':';
        if (str_starts_with($payload, $prefix)) {
            $id  = (int) substr($payload, strlen($prefix));
            $btn = CategoryState::query()->with('category')->findOrFail($id);

            DB::transaction(function () use ($btn) {
                $user = $this->process();

                if ($btn->price > 0) {
                    UserCart::add($user, (int) $btn->price);
                }

                $data = (array) ($user->tg_data ?? []);
                $slug = $btn->category->slug;
                $data['choices'][$slug]     = $btn->code ?? $btn->title_key;
                $data['choices_ids'][$slug] = $btn->id;
                $user->forceFill(['tg_data' => $data])->save();
            });

            if ($next = $this->stateKey->next()) {
                $this->goKey($next->value);
            } else {
                $this->goKey(StateKey::BuyConfirm->value);
            }
            return;
        }

        $this->silent();
    }

    protected function handleBack(StateKey $prev): void
    {
        $user = $this->process();
        $prevSlug = $prev->categorySlug();

        $data = (array) ($user->tg_data ?? []);
        $pickedId = (int) ($data['choices_ids'][$prevSlug] ?? 0);

        if ($pickedId > 0) {
            if ($btn = CategoryState::query()->find($pickedId)) {
                if ($btn->price > 0) {
                    UserCart::sub($user, (int) $btn->price);
                }
            }
        }

        unset($data['choices'][$prevSlug], $data['choices_ids'][$prevSlug]);
        $user->forceFill(['tg_data' => $data])->save();

        $this->goKey($prev->value);
    }
}
