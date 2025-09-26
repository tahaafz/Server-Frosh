<?php

namespace App\Jobs;

use App\DTOs\ServerActionDTO;
use App\Models\User;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Telegram\Bot\Laravel\Facades\Telegram;

class GcoreServerActionJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(public ServerActionDTO $dto) {}

    public function handle(): void
    {
        $user = User::find($this->dto->user_id);
        $srv  = Server::find($this->dto->server_id);
        if (!$user || !$srv || !$srv->external_id) return;

        $apiKey  = config('services.gcore.api_key');
        $project = '460993';
        $base    = "https://api.gcore.com/cloud/v1/instances/{$project}/{$srv->region_id}/{$srv->external_id}";

        if ($this->dto->action === 'delete') {
            $r = Http::withHeaders(['Authorization'=>"APIKey {$apiKey}"])
                ->withOptions(['timeout'=>30])
                ->delete($base);
        } else {
            $r = Http::withHeaders(['Authorization'=>"APIKey {$apiKey}"])
                ->withOptions(['timeout'=>30])
                ->post("{$base}/{$this->dto->action}");
        }

        if (!$r->successful()) {
            Telegram::sendMessage([
                'chat_id'    => $user->telegram_chat_id,
                'parse_mode' => 'HTML',
                'text'       => "❌ اجرای عملیات «{$this->dto->action}» برای سرور <code>{$srv->name}</code> ناموفق بود.\n<code>".htmlspecialchars(Str::limit($r->body(), 800))."</code>"
            ]);
            return;
        }

        $newStatus = match($this->dto->action) {
            'start'  => 'starting',
            'stop'   => 'stopping',
            'delete' => 'deleting',
            default  => $srv->status,
        };
        $srv->status = $newStatus;
        $srv->save();

        $human = match($this->dto->action) { 'start'=>'راه‌اندازی', 'stop'=>'خاموش', 'delete'=>'حذف' };
        Telegram::sendMessage([
            'chat_id'    => $user->telegram_chat_id,
            'parse_mode' => 'HTML',
            'text'       => "✅ درخواست «{$human}» برای سرور <code>{$srv->name}</code> ارسال شد.\nممکن است اعمال کامل چند لحظه زمان ببرد."
        ]);
    }
}
