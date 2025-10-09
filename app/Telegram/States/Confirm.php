<?php

namespace App\Telegram\States;

use App\DTOs\ServerCreateDTO;
use App\Enums\Telegram\StateKey;
use App\Jobs\Telegram\CreateServerJob;
use App\Telegram\Core\AbstractState;
use App\Telegram\UI\Buttons;
use App\Telegram\UI\ManagesScreens;
use Illuminate\Support\Str;

class Confirm extends AbstractState
{
    use ManagesScreens;

    public function onEnter(): void
    {
        $summary = __('telegram.buy.summary_title')."\n"
            . '• Provider: <code>'.strtoupper($this->getData('provider', 'gcore'))."</code>\n"
            . '• Plan: <code>'.$this->getData('plan_code', '—')."</code>\n"
            . '• Region: <code>'.$this->getData('region_id', '—')."</code>\n"
            . '• OS: <code>'.$this->getData('os_image_id', '—')."</code>";

        $inline = [
            [
                [
                    'text' => Buttons::label('buy.confirm_and_send'),
                    'callback_data' => $this->pack('confirm:yes'),
                ],
            ],
            [
                [
                    'text' => Buttons::label('buy.back'),
                    'callback_data' => $this->pack('back:os'),
                ],
            ],
        ];

        $this->ensureInlineScreen($summary, ['inline_keyboard' => $inline]);
    }

    public function onCallback(string $data, array $update): void
    {
        [$ok, $rest] = $this->validateCallback($data, $update);
        if (! $ok || $rest === null) {
            $this->onEnter();

            return;
        }

        if ($rest === 'confirm:yes') {
            $user = $this->process();

            $vmName = $user->telegram_user_id.'-'.Str::upper(Str::random(6));
            $pass   = Str::random(14);

            $this->putData('vm_name', $vmName);

            $dto = ServerCreateDTO::fromArray([
                'user_id'     => $user->id,
                'provider'    => $this->getData('provider', 'gcore'),
                'plan'        => $this->getData('plan_code'),
                'region_id'   => $this->getData('region_id'),
                'os_image_id' => $this->getData('os_image_id'),
                'vm_name'     => $vmName,
                'login_user'  => 'ubuntu',
                'login_pass'  => $pass,
            ]);

            CreateServerJob::dispatch($dto);

            $this->send(__('telegram.buy.submitted'));

            return;
        }

        if ($rest === 'back:os') {
            $this->goEnum(StateKey::BuyChooseOS);

            return;
        }

        if ($rest === 'back:welcome') {
            $this->resetToWelcomeMenu();

            return;
        }

        $this->onEnter();
    }
}
