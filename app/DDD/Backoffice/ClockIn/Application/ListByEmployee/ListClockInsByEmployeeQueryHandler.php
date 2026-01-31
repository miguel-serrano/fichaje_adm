<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\ListByEmployee;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\ClockIn\Domain\ClockIn;
use App\DDD\Backoffice\ClockIn\Domain\Interface\ClockInRepositoryInterface;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInType;
use App\DDD\Backoffice\ClockIn\Domain\Voter\ClockInVoter;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use DateTimeImmutable;

final class ListClockInsByEmployeeQueryHandler
{
    public function __construct(
        private readonly ClockInRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(ListClockInsByEmployeeQuery $query): ListClockInsByEmployeeResponse
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: ClockInVoter::view(),
            userId: $query->activeUserId,
        );

        $clockIns = $this->repository->findByEmployeeAndDateRange(
            employeeId: EmployeeId::create($query->employeeId),
            startDate: new DateTimeImmutable($query->startDate),
            endDate: new DateTimeImmutable($query->endDate),
        );

        $items = array_map(
            fn (ClockIn $clockIn) => $this->mapToItem($clockIn),
            $clockIns,
        );

        return ListClockInsByEmployeeResponse::create($items);
    }

    private function mapToItem(ClockIn $clockIn): array
    {
        return [
            'id' => $clockIn->id()->value(),
            'employee_id' => $clockIn->employeeId()->value(),
            'type' => $clockIn->type()->value,
            'type_label' => $clockIn->type()->label(),
            'timestamp' => $clockIn->timestamp()->toString(),
            'date' => $clockIn->timestamp()->toDateString(),
            'time' => $clockIn->timestamp()->toTimeString(),
            'workplace_id' => $clockIn->workplaceId()?->value(),
            'latitude' => $clockIn->latitude()?->value(),
            'longitude' => $clockIn->longitude()?->value(),
            'notes' => $clockIn->notes()?->value(),
        ];
    }
}
