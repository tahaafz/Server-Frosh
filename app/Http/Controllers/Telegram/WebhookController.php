<?php

namespace App\Http\Controllers\Telegram;

use App\DTOs\Telegram\TelegramUpdateDTO;
use App\Pipelines\Telegram\Pipes\BootTelegramUser;
use App\Pipelines\Telegram\Pipes\CheckSpam;
use App\Pipelines\Telegram\Pipes\EnforceChannelGate;
use App\Pipelines\Telegram\Pipes\EnforcePrivateChat;
use App\Pipelines\Telegram\Pipes\EnsureIdentifiers;
use App\Pipelines\Telegram\Pipes\EnsureUserNotBlocked;
use App\Pipelines\Telegram\Pipes\HandleAdminInbox;
use App\Pipelines\Telegram\Pipes\HandleStartCommand;
use App\Pipelines\Telegram\Pipes\HandleState;
use App\Pipelines\Telegram\Pipes\PreventDuplicate;
use App\Pipelines\Telegram\WebhookPayload;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Controller;

class WebhookController extends Controller
{
    public function __invoke(Request $request, Pipeline $pipeline)
    {
        $payload = new WebhookPayload(TelegramUpdateDTO::from($request->all()));

        return $pipeline
            ->send($payload)
            ->through([
                EnsureIdentifiers::class,
                PreventDuplicate::class,
                EnforcePrivateChat::class,
                BootTelegramUser::class,
                EnsureUserNotBlocked::class,
                CheckSpam::class,
                HandleAdminInbox::class,
                EnforceChannelGate::class,
                HandleStartCommand::class,
                HandleState::class,
            ])
            ->then(fn () => response('ok'));
    }
}
