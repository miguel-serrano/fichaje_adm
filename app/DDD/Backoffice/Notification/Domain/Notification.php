<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain;

use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationId;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationType;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationTitle;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationBody;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationChannel;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationStatus;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationRecipientId;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationData;
use DateTimeImmutable;

class Notification
{
    private ?DateTimeImmutable $readAt = null;
    private ?DateTimeImmutable $sentAt = null;

    protected function __construct(
        private readonly NotificationId $id,
        private readonly NotificationType $type,
        private readonly NotificationTitle $title,
        private readonly NotificationBody $body,
        private readonly NotificationChannel $channel,
        private NotificationStatus $status,
        private readonly NotificationRecipientId $recipientId,
        private readonly ?NotificationData $data,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        NotificationId $id,
        NotificationType $type,
        NotificationTitle $title,
        NotificationBody $body,
        NotificationChannel $channel,
        NotificationRecipientId $recipientId,
        ?NotificationData $data = null,
    ): self {
        return new self(
            id: $id,
            type: $type,
            title: $title,
            body: $body,
            channel: $channel,
            status: NotificationStatus::PENDING,
            recipientId: $recipientId,
            data: $data,
            createdAt: new DateTimeImmutable(),
        );
    }

    public static function fromPrimitives(array $data): self
    {
        $notification = new self(
            id: NotificationId::create($data['id']),
            type: NotificationType::from($data['type']),
            title: NotificationTitle::create($data['title']),
            body: NotificationBody::create($data['body']),
            channel: NotificationChannel::from($data['channel']),
            status: NotificationStatus::from($data['status']),
            recipientId: NotificationRecipientId::create($data['recipient_id']),
            data: isset($data['data']) ? NotificationData::fromArray($data['data']) : null,
            createdAt: new DateTimeImmutable($data['created_at']),
        );

        if (isset($data['read_at'])) {
            $notification->readAt = new DateTimeImmutable($data['read_at']);
        }

        if (isset($data['sent_at'])) {
            $notification->sentAt = new DateTimeImmutable($data['sent_at']);
        }

        return $notification;
    }

    public function id(): NotificationId
    {
        return $this->id;
    }

    public function type(): NotificationType
    {
        return $this->type;
    }

    public function title(): NotificationTitle
    {
        return $this->title;
    }

    public function body(): NotificationBody
    {
        return $this->body;
    }

    public function channel(): NotificationChannel
    {
        return $this->channel;
    }

    public function status(): NotificationStatus
    {
        return $this->status;
    }

    public function recipientId(): NotificationRecipientId
    {
        return $this->recipientId;
    }

    public function data(): ?NotificationData
    {
        return $this->data;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function readAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function sentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function isPending(): bool
    {
        return $this->status === NotificationStatus::PENDING;
    }

    public function isSent(): bool
    {
        return $this->status === NotificationStatus::SENT;
    }

    public function isRead(): bool
    {
        return $this->readAt !== null;
    }

    public function isFailed(): bool
    {
        return $this->status === NotificationStatus::FAILED;
    }

    public function markAsSent(): void
    {
        $this->status = NotificationStatus::SENT;
        $this->sentAt = new DateTimeImmutable();
    }

    public function markAsRead(): void
    {
        if ($this->readAt === null) {
            $this->readAt = new DateTimeImmutable();
        }
    }

    public function markAsFailed(): void
    {
        $this->status = NotificationStatus::FAILED;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type->value,
            'title' => $this->title->value(),
            'body' => $this->body->value(),
            'channel' => $this->channel->value,
            'status' => $this->status->value,
            'recipient_id' => $this->recipientId->value(),
            'data' => $this->data?->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'read_at' => $this->readAt?->format('Y-m-d H:i:s'),
            'sent_at' => $this->sentAt?->format('Y-m-d H:i:s'),
        ];
    }
}
