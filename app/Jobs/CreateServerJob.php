<?php

namespace App\Jobs;

use App\DTOs\ServerDTO;
use App\Models\Server;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class CreateServerJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $serverDTO;

    public function __construct(ServerDTO $serverDTO)
    {
        $this->serverDTO = $serverDTO;
    }

    public function handle()
    {
        $apiKey = 'your-api-key';  // کلید API خود را وارد کنید
        $apiUrl = "https://api.gcore.com/cloud/v2/instances/460993/{$this->serverDTO->toArray()['location']}";

        // استفاده از Http Client لاراول به جای curl
        $response = Http::withHeaders([
            'Authorization' => "APIKey $apiKey"
        ])
            ->post($apiUrl, [
                'flavor' => $this->serverDTO->toArray()['plan'],
                'names' => [$this->serverDTO->toArray()['name']],
                'interfaces' => [
                    ['type' => 'external'],
                ],
                'user_data' => base64_encode("#cloud-config\npassword: {$this->serverDTO->toArray()['name']}\nchpasswd: { expire:False }\nssh_pwauth: True"),
                'volumes' => [
                    ['image_id' => $this->serverDTO->toArray()['os'], 'source' => 'image', 'metadata' => []]
                ]
            ]);

        if ($response->successful()) {
            $serverData = $response->json();

            // ذخیره اطلاعات سرور در دیتابیس
            $server = Server::create([
                'user_id' => $this->serverDTO->user_id,
                'server_id' => $serverData['id'],
                'name' => $this->serverDTO->name,
                'ip_address' => $serverData['ip_address'],
                'status' => 'active',
            ]);

            // ارسال پیام به کاربر با اطلاعات سرور ساخته شده
            $this->sendServerInfoToUser($server);
        } else {
            \Log::error('Server creation failed: ' . $response->body());
        }
    }

    // ارسال اطلاعات سرور به کاربر
    private function sendServerInfoToUser(Server $server)
    {
        // اینجا پیام و اطلاعات سرور را به کاربر ارسال می‌کنیم
        // می‌توانیم اطلاعات سرور را از طریق Telegram API ارسال کنیم.
    }
}
