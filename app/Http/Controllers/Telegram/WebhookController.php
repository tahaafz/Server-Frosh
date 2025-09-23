<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Telegram\Fsm\Core\Context;
use App\Telegram\Fsm\Core\Registry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $u = $request->all();

        $chatId = data_get($u,'callback_query.message.chat.id') ?? data_get($u,'message.chat.id');
        $tgUserId = data_get($u,'callback_query.from.id') ?? data_get($u,'message.from.id');
        $text = data_get($u,'message.text');
        $updateId = data_get($u,'update_id');

        if (!$chatId || !$tgUserId) return response('ok');

        if ($updateId && !Cache::add('tg:update:'.$updateId, 1, now()->addMinutes(3))) {
            return response('ok');
        }

        $user = User::firstOrCreate(
            ['telegram_user_id' => $tgUserId],
            [
                'telegram_chat_id' => $chatId,
                'name' => 'tg-'.$tgUserId,
                'email' => 'tg_'.$tgUserId.'@example.com',
                'password' => bcrypt(str()->random(16)),
            ]
        );
        if ($user->telegram_chat_id !== $chatId) {
            $user->telegram_chat_id = $chatId;
            $user->save();
        }

        $norm = $text ? mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]/u','',$text)),'UTF-8') : null;
        if (!$user->tg_current_state || $norm === '/start' || $norm === 'start') {
            $user->tg_current_state = 'welcome';
            if ($norm === '/start') { $user->tg_data = null; $user->tg_last_message_id = null; }
            $user->save();

            (new Context($user, Registry::map()))->getState()->onEnter();
            return response('ok');
        }

        $ctx = new Context($user, Registry::map());
        $ctx->getState()->handle($u);

        return response('ok');
    }
}
