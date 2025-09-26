<?php

namespace App\Telegram\States;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\ReadsUpdate;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\PersistsData;
use App\DTOs\ServerCreateDTO;
use App\Jobs\CreateServerJob;
use Illuminate\Support\Str;
use App\Telegram\Fsm\Traits\FlowToken;
use App\Telegram\Fsm\Traits\MainMenuShortcuts;

class Confirm extends State
{
    use ReadsUpdate, SendsMessages, PersistsData, MainMenuShortcuts, FlowToken;

    public function onEnter(): void
    {
        $txt = "🧾 خلاصه سفارش:\n"
            . "• Provider: <code>".strtoupper($this->getData('provider','gcore'))."</code>\n"
            . "• Plan: <code>".$this->getData('plan','—')."</code>\n"
            . "• Region: <code>".$this->getData('region_id','—')."</code>\n"
            . "• OS: <code>".$this->getData('os_image_id','—')."</code>";

        $kb = $this->inlineKeyboard([
            [ ['text'=>'✅ تایید و ارسال','data'=>$this->pack('confirm:yes')] ],
            [ ['text'=>'⬅️ برگشت','data'=>$this->pack('back:os')] ],
        ]);
        $this->edit($txt, $kb);
    }

    public function onCallback(string $data, array $u): void
    {
        [$ok,$rest] = $this->validateCallback($data,$u);
        if (!$ok) return;

        if ($rest === 'confirm:yes') {
            $user = $this->process();

            // ساخت نام و پسورد
            $vmName = $user->telegram_user_id.'-'.Str::upper(Str::random(6));
            $pass   = Str::random(14);

            // ذخیره موقت برای شفافیت (اختیاری)
            $this->putData('vm_name', $vmName);

            // DTO
            $dto = ServerCreateDTO::fromArray([
                'user_id'     => $user->id,
                'provider'    => $this->getData('provider','gcore'),
                'plan'        => $this->getData('plan'),
                'region_id'   => $this->getData('region_id'),
                'os_image_id' => $this->getData('os_image_id'),
                'vm_name'     => $vmName,
                'login_user'  => 'ubuntu',
                'login_pass'  => $pass,
            ]);

            CreateServerJob::dispatch($dto);

            $this->send(
                "✅ درخواست شما ثبت شد.\n".
                "پس از ساخت، مشخصات اتصال برایتان ارسال می‌شود."
            );
            return;
        }
        if ($rest === 'back:os') {
            $this->parent->transitionTo('buy.choose_os'); return;
        }
        $this->onEnter();
    }
}
