<?php

namespace App\Telegram\States\Servers;

use App\Telegram\UI\Buttons;

class ListServers extends \App\Telegram\Core\AbstractState
{
    public function onEnter(): void
    {
        $user = $this->process();
        $servers = $user->servers()->latest()->take(10)->get();

        if ($servers->isEmpty()) {
            $this->sendT('telegram.servers.list.none');
            return;
        }

        $rows = [];
        foreach ($servers as $srv) {
            $label = "{$srv->name} • {$srv->status}".($srv->ip_address ? " • {$srv->ip_address}" : "");
            $rows[] = [ ['text' => $label, 'data' => "srv:panel:{$srv->id}"] ];
        }
        $rows[] = [ ['text' => Buttons::label('back'), 'data' => 'nav:welcome'] ];

        $this->sendT('telegram.servers.list.title', ['inline_keyboard' => $rows]);
    }

    public function onCallback(string $data, array $u): void
    {
        if (str_starts_with($data, 'srv:panel:')) {
            $this->goEnum(\App\Enums\Telegram\StateKey::ServersPanel);
            return;
        }
        if ($data === 'nav:welcome') {
            $this->goEnum(\App\Enums\Telegram\StateKey::Welcome);
        }
        $this->onEnter();
    }
}
