<?php

namespace App\Telegram\States;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;
use App\Jobs\CreateServerJob;
use App\DTOs\ServerDTO;

class Confirm extends State
{
    use ReadsUpdate, SendsMessages, PersistsData;

    public function onEnter(): void
    {
        $plan = $this->getData('plan');
        $location = $this->getData('location');
        $os = $this->getData('os');

        // ارسال تایید به کاربر
        $this->send(
            "درخواست شما ثبت شد: Plan: $plan | Location: $location | OS: $os",
            $this->inlineKeyboard([
                [
                    ['text' => 'تایید و ارسال', 'data' => 'confirm_yes'],
                ],
            ])
        );
    }

    public function onCallback(string $data, array $u): void
    {
        if ($data === 'confirm_yes') {
            $user = $this->process();

            // ایجاد DTO برای ارسال به Job
            $serverDTO = new ServerDTO(
                $user->id,
                $this->getData('server_id'),
                $this->getData('name'),
                $this->getData('ip_address'),
                'pending' // وضعیت اولیه
            );

            // ارسال Job برای ساخت سرور به صف
            CreateServerJob::dispatch($serverDTO);

            $this->send("درخواست ساخت سرور شما ثبت شد، لطفاً منتظر بمانید.");
        }
    }
}
