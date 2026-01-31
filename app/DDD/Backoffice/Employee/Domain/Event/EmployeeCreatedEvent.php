<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Event;

use App\DDD\Shared\Domain\Event\AbstractDomainEvent;

final class EmployeeCreatedEvent extends AbstractDomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly string $name,
        private readonly string $lastName,
        private readonly string $email,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'employee.created';
    }

    public function name(): string
    {
        return $this->name;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'name' => $this->name,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
