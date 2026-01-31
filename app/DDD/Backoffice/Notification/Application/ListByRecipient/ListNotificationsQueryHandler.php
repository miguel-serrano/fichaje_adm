<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\ListByRecipient;

use App\DDD\Backoffice\Notification\Domain\Interface\NotificationRepositoryInterface;
use App\DDD\Backoffice\Notification\Domain\Notification;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationRecipientId;

final class ListNotificationsQueryHandler
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
    ) {}

    public function __invoke(ListNotificationsQuery $query): ListNotificationsResponse
    {
        $recipientId = NotificationRecipientId::create($query->recipientId);

        $notifications = $query->onlyUnread
            ? $this->repository->findUnreadByRecipient($recipientId)
            : $this->repository->findByRecipient($recipientId, $query->limit);

        $unreadCount = $this->repository->countUnreadByRecipient($recipientId);

        $items = array_map(
            fn (Notification $notification) => NotificationItem::fromNotification($notification),
            $notifications,
        );

        return new ListNotificationsResponse($items, $unreadCount);
    }
}
