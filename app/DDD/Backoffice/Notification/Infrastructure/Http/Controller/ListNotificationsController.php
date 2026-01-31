<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Notification\Application\ListByRecipient\ListNotificationsQuery;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ListNotificationsController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        $response = $this->queryBus->ask(new ListNotificationsQuery(
            recipientId: $this->activeUserId(),
        ));

        return Inertia::render('Backoffice/Notifications/Index', $response->toArray());
    }
}
