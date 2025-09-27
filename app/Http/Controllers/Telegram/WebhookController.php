<?php

namespace App\Http\Controllers\Telegram;

use App\DTOs\Telegram\TelegramUpdateDTO;
use App\Gurds\Telegram\ChannelGate;
use App\Gurds\Telegram\PrivateChatGate;
use App\Gurds\Telegram\SpamGuard;
use App\Gurds\Telegram\UpdateDeduplicator;
use App\Services\Telegram\Admin\AdminInboxRouter;
use App\Services\Telegram\TelegramUserService;
use App\Services\Telegram\TopupApprovalService;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Core\Context;
use App\Telegram\Core\Registry;
use App\Traits\Telegram\TgApi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebhookController extends Controller
{
    use TgApi;

    public function __invoke(
        Request             $request,
        ChannelGate         $gate,
        SpamGuard           $spam,
        UpdateDeduplicator  $dedup,
        PrivateChatGate     $pvGate,
        TelegramUserService $users,
        StartCommand        $start,
        AdminInboxRouter $adminInbox,
    ) {
        $dto = TelegramUpdateDTO::from($request->all());

        if (!$dto->chatId || !$dto->userId) return response('ok');

        if (!$dedup->shouldProcess($dto->updateId)) return response('ok');

        if (!$pvGate->enforce($dto)) return response('ok');

        $user = $users->bootOrUpdate($dto);

        if ($user->is_blocked) {
            $this->tgSend($user->telegram_chat_id,
                "🚫 شما توسط مدیریت مسدود شده‌اید.\n".
                ($user->blocked_reason ? "دلیل: {$user->blocked_reason}" : "")
            );
            return response('ok');
        }

        if (!$spam->checkOrBlock($user)) return response('ok');

        if ($adminInbox->maybeHandle($user, $dto)) {
            return response('ok');
        }

        if (!$user->is_admin && $gate->isChannelLockOn()) {
            if ($dto->cbData === 'confirm:channel') {
                if ($gate->confirmOrAlert($dto->raw, $dto->chatId, $dto->userId)) {
                    $start->resetToWelcome($user);
                }
                return response('ok');
            }
            if (!$gate->isMember($dto->userId)) {
                $gate->sendJoinPrompt($dto->chatId);
                return response('ok');
            }
        }

        if ($start->maybe($user, $dto->text)) return response('ok');

        (new Context($user, Registry::map()))->getState()->handle($dto->raw);
        return response('ok');
    }
}
