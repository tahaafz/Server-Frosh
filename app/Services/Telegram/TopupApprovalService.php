<?php
namespace App\Services\Telegram;

use App\Models\TopupRequest;
use App\Models\User;
use App\Services\WalletService;
use App\Traits\Telegram\TgApi;
use Illuminate\Support\Facades\DB;

class TopupApprovalService
{
    use TgApi;

    public function handle(User $admin, string $action, int $id): void
    {
        if (!$admin->is_admin) return;

        DB::transaction(function () use ($admin, $action, $id) {
            $req = TopupRequest::lockForUpdate()->find($id);
            if (!$req || $req->status !== 'pending') return;

            $user = $req->user;
            if (!$user) return;

            if ($action === 'approve') {
                $req->status = 'approved';
                $req->admin_id = $admin->id;
                $req->approved_at = now();
                $req->save();

                app(WalletService::class)->credit($user, $req->amount, "topup:{$req->id}", [
                    'method'=>$req->method, 'admin_id'=>$admin->id
                ]);

                $this->tgSend($user->telegram_chat_id,
                    "✅ شارژ کیف پول تایید شد.\nمبلغ: <b>".number_format($req->amount)."</b> تومان\n"
                    . "موجودی فعلی: <b>".number_format($user->balance)."</b> تومان"
                );

            } elseif ($action === 'reject') {
                $req->status = 'rejected';
                $req->admin_id = $admin->id;
                $req->save();

                $this->tgSend($user->telegram_chat_id,
                    "❌ رسید شما تایید نشد. در صورت سوال، با پشتیبانی در ارتباط باشید."
                );
            }
        });
    }
}
