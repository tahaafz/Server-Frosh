<?php

namespace App\Pipelines\Telegram\Pipes;

use App\Gurds\Telegram\UpdateDeduplicator;
use App\Pipelines\Telegram\WebhookPayload;
use Closure;

class PreventDuplicate
{
    public function __construct(private UpdateDeduplicator $deduplicator)
    {
    }

    public function handle(WebhookPayload $payload, Closure $next)
    {
        if (!$this->deduplicator->shouldProcess($payload->dto->updateId)) {
            return response('ok');
        }

        return $next($payload);
    }
}
