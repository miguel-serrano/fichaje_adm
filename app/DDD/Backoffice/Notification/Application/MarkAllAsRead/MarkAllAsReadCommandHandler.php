<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\MarkAllAsRead;

use App\DDD\Backoffice\Notification\Domain\Interface\NotificationRepositoryInterface;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationRecipientId;

final class MarkAllAsReadCommandHandler
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
    ) {}

    public function __invoke(MarkAllAsReadCommand $command): void
    {
        $this->repository->markAllAsReadForRecipient(
            NotificationRecipientId::create($command->recipientId)
        );
    }
}
