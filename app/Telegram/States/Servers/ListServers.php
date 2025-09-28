<?php

namespace App\Telegram\States\Servers;

use App\Enums\Telegram\StateKey;
use App\Telegram\Core\State;
use App\Traits\Telegram\ReadsUpdate;
use App\Traits\Telegram\SendsMessages;
use App\Telegram\UI\Buttons;

class ListServers extends State
{
    use SendsMessages, ReadsUpdate;

    public function onEnter(): void
    {
        $user = $this->process();
        $servers = $user->servers()->latest()->take(10)->get();

        if ($servers->isEmpty()) {
            $this->send(__('telegram.servers.list.none'));
            return;
        }

        $rows = [];
        foreach ($servers as $srv) {
            $label = "{$srv->name} • {$srv->status}".($srv->ip_address ? " • {$srv->ip_address}" : "");
            $rows[] = [ ['text' => $label, 'data' => "srv:panel:{$srv->id}"] ];
        }
        $rows[] = [ ['text' => Buttons::label('back'), 'data' => 'nav:welcome'] ];

        $this->send(__('telegram.servers.list.title'), ['inline_keyboard' => $rows]);
    }

    public function onCallback(string $data, array $u): void
    {
        if (str_starts_with($data, 'srv:panel:')) {
            $this->parent->transitionTo(StateKey::ServersPanel->value);
            return;
        }
        if ($data === 'nav:welcome') {
            $this->parent->transitionTo(StateKey::Welcome->value);
        }
        $this->onEnter();
    }
}
