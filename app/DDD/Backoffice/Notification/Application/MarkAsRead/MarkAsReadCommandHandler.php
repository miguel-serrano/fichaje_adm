<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\MarkAsRead;

use App\DDD\Backoffice\Notification\Domain\Interface\NotificationRepositoryInterface;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationId;

final class MarkAsReadCommandHandler
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
    ) {}

    public function __invoke(MarkAsReadCommand $command): void
    {
        $notification = $this->repository->findById(
            NotificationId::create($command->notificationId)
        );

        if ($notification === null) {
            return;
        }

        if ($notification->recipientId()->value() !== $command->recipientId) {
            return;
        }

        $notification->markAsRead();

        $this->repository->save($notification);
    }
}
