<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Notification\Application\MarkAsRead\MarkAsReadCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class MarkNotificationAsReadController extends BaseController
{
    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new MarkAsReadCommand(
            notificationId: $id,
            recipientId: $this->activeUserId(),
        ));

        return redirect()->back()->with('success', 'Notificación marcada como leída.');
    }
}
