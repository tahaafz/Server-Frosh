<?php

namespace App\Listeners;

use App\Events\WalletTopupApproved;
use App\Enums\Telegram\StateKey;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResumeAfterTopup implements ShouldQueue
{
    public function handle(WalletTopupApproved $event): void
    {
        $user = $event->user->fresh();

        $data = (array) ($user->tg_data ?? []);
        $resume = (array) ($data['resume'] ?? null);
        if (empty($resume)) {
            return;
        }

        unset($data['resume']);
        $user->forceFill(['tg_data' => $data])->save();


        $this->goToState($user, StateKey::BuyConfirm);
    }

    protected function goToState(\App\Models\User $user, StateKey $key): void
    {
        app(\App\Telegram\Support\StateRunner::class)->go($user, $key);
    }
}
