<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Notification\Application\MarkAllAsRead\MarkAllAsReadCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class MarkAllNotificationsAsReadController extends BaseController
{
    public function __invoke(): RedirectResponse
    {
        $this->commandBus->dispatch(new MarkAllAsReadCommand(
            recipientId: $this->activeUserId(),
        ));

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
}
