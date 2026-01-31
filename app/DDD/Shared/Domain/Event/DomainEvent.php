<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function eventId(): string;

    public function occurredOn(): DateTimeImmutable;

    public function aggregateId(): string;

    public static function eventName(): string;

    public function toPrimitives(): array;
}
