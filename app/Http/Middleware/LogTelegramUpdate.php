<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogTelegramUpdate
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->all();
        Log::channel('telegram')->info('Incoming update', [
            'update_id' => data_get($u,'update_id'),
            'from_id'   => data_get($u,'message.from.id') ?? data_get($u,'callback_query.from.id'),
            'chat_id'   => data_get($u,'message.chat.id') ?? data_get($u,'callback_query.message.chat.id'),
            'type'      => data_get($u,'message') ? 'message' : (data_get($u,'callback_query') ? 'callback' : 'other'),
            'payload'   => $u,
        ]);

        $resp = $next($request);

        Log::channel('telegram')->info('Outgoing response', [
            'status'    => $resp->status(),
            'update_id' => data_get($u,'update_id'),
        ]);

        return $resp;
    }
}
