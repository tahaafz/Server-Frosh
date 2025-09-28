<?php

namespace App\Jobs\Telegarm;

use App\DTOs\ServerActionDTO;
use App\Models\Server;
use App\Models\User;
use App\Services\AdminNotifier;
use App\Traits\Telegram\TgApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GcoreServerActionJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, TgApi;

    public function __construct(public ServerActionDTO $dto)
    {
    }

    public function handle(): void
    {
        $user = User::find($this->dto->user_id);
        $srv = Server::find($this->dto->server_id);
        if (!$user || !$srv || !$srv->external_id) return;

        $apiKey = config('services.gcore.api_key');
        $project = '460993';
        $base = "https://api.gcore.com/cloud/v1/instances/{$project}/{$srv->region_id}/{$srv->external_id}";

        if ($this->dto->action === 'delete') {
            $r = Http::withHeaders(['Authorization' => "APIKey {$apiKey}"])
                ->withOptions(['timeout' => 30])
                ->delete($base);
        } else {
            $r = Http::withHeaders(['Authorization' => "APIKey {$apiKey}"])
                ->withOptions(['timeout' => 30])
                ->post("{$base}/{$this->dto->action}");
        }

        if (!$r->successful()) {
            $this->tgSend(
                $user->telegram_chat_id,
                __('telegram.servers.action_failed', ['name' => $srv->name])
            );
            app(AdminNotifier::class)->serverActionFailed($srv, $this->dto->action, $r->body());
            return;
        }

        $newStatus = match ($this->dto->action) {
            'start' => 'starting',
            'stop' => 'stopping',
            'delete' => 'deleting',
            default => $srv->status,
        };
        $srv->status = $newStatus;
        $srv->save();

        $human = match($this->dto->action) {
            'start' => __('telegram.servers.action_name.start'),
            'stop'  => __('telegram.servers.action_name.stop'),
            'delete'=> __('telegram.servers.action_name.delete'),
            default => $this->dto->action,
        };
        $this->tgSend(
            $user->telegram_chat_id,
            __('telegram.servers.action_requested', ['action' => $human, 'name' => $srv->name])
        );
    }
}
