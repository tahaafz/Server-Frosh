<?php
namespace App\Payments;

use App\Payments\PaymentMethod;
use App\Models\TopupRequest;

class CardPayment implements PaymentMethod
{
    public function key(): string { return 'card'; }

    public function instruction(TopupRequest $req): string
    {
        $card = config('payment.card_number');
        $name = config('payment.card_holder');
        $amt  = number_format($req->amount);

        return "💳 اطلاعات واریز کارت به کارت:\n"
            . "به نام: <b>{$name}</b>\n"
            . "شماره کارت: <code>{$card}</code>\n\n"
            . "مبلغ: <b>{$amt}</b> تومان\n\n"
            . "لطفاً پس از واریز، <b>عکس رسید</b> را در همین گفتگو ارسال کنید.";
    }

    public function keyboard(TopupRequest $req): ?array
    {
        return ['inline_keyboard' => [
            [ [ 'text' => 'لغو', 'callback_data' => "topup:cancel:{$req->id}" ] ],
        ]];
    }
}
