<?php

declare(strict_types=1);

namespace Tests\Unit\DDD\Backoffice\ClockIn\Domain\Event;

use App\DDD\Backoffice\ClockIn\Domain\Event\ClockInCreatedEvent;
use App\DDD\Backoffice\ClockIn\Domain\Event\LateArrivalDetectedEvent;
use PHPUnit\Framework\TestCase;

final class ClockInEventsTest extends TestCase
{
    public function test_clock_in_created_event(): void
    {
        $event = new ClockInCreatedEvent(
            aggregateId: '123',
            employeeId: 456,
            type: 'entry',
            timestamp: '2025-01-30 09:00:00',
            latitude: 40.4168,
            longitude: -3.7038,
            workplaceId: 1,
        );

        $this->assertEquals('123', $event->aggregateId());
        $this->assertEquals(456, $event->employeeId());
        $this->assertEquals('entry', $event->type());
        $this->assertEquals('clock_in.created', $event::eventName());
        $this->assertNotEmpty($event->eventId());
        $this->assertNotNull($event->occurredOn());
    }

    public function test_clock_in_created_event_to_primitives(): void
    {
        $event = new ClockInCreatedEvent(
            aggregateId: '123',
            employeeId: 456,
            type: 'entry',
            timestamp: '2025-01-30 09:00:00',
            latitude: 40.4168,
            longitude: -3.7038,
            workplaceId: 1,
        );

        $primitives = $event->toPrimitives();

        $this->assertEquals('123', $primitives['aggregate_id']);
        $this->assertEquals(456, $primitives['employee_id']);
        $this->assertEquals('entry', $primitives['type']);
        $this->assertEquals(1, $primitives['workplace_id']);
    }

    public function test_late_arrival_detected_event(): void
    {
        $event = new LateArrivalDetectedEvent(
            aggregateId: '123',
            employeeId: 456,
            employeeName: 'Juan García',
            date: '2025-01-30',
            minutesLate: 15,
            expectedTime: '09:00',
            actualTime: '09:15',
        );

        $this->assertEquals('clock_in.late_arrival_detected', $event::eventName());
        $this->assertEquals(456, $event->employeeId());
        $this->assertEquals('Juan García', $event->employeeName());
        $this->assertEquals(15, $event->minutesLate());
    }
}
