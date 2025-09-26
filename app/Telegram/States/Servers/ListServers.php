<?php

namespace App\Telegram\States\Servers;

use App\Telegram\Fsm\Core\State;
use App\Telegram\Fsm\Traits\SendsMessages;
use App\Telegram\Fsm\Traits\ReadsUpdate;

class ListServers extends State
{
    use SendsMessages, ReadsUpdate;

    public function onEnter(): void
    {
        $user = $this->process();
        $servers = $user->servers()->latest()->take(10)->get();

        if ($servers->isEmpty()) {
            $this->send("هنوز سروری ندارید. از «خرید VPS» برای ساخت سرور جدید استفاده کنید.");
            return;
        }

        $rows = [];
        foreach ($servers as $srv) {
            $label = "{$srv->name} • {$srv->status}".($srv->ip_address ? " • {$srv->ip_address}" : "");
            $rows[] = [ ['text' => $label, 'data' => "srv:panel:{$srv->id}"] ];
        }
        $rows[] = [ ['text' => '⬅️ بازگشت', 'data' => 'nav:welcome'] ];

        $this->send("📄 لیست سرورهای شما:", ['inline_keyboard' => $rows]);
    }

    public function onCallback(string $data, array $u): void
    {
        if (str_starts_with($data, 'srv:panel:')) {
            $this->parent->transitionTo('servers.panel');
            return;
        }
        if ($data === 'nav:welcome') {
            $this->parent->transitionTo('welcome'); return;
        }
        $this->onEnter();
    }
}
