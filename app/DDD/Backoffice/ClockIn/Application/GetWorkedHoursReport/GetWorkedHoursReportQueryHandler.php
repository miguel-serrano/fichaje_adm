<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\GetWorkedHoursReport;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\ClockIn\Domain\Interface\ClockInRepositoryInterface;
use App\DDD\Backoffice\ClockIn\Domain\Service\WorkedHoursCalculator;
use App\DDD\Backoffice\ClockIn\Domain\Voter\ClockInVoter;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use DateTimeImmutable;

final class GetWorkedHoursReportQueryHandler
{
    public function __construct(
        private readonly ClockInRepositoryInterface $clockInRepository,
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly WorkedHoursCalculator $calculator,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(GetWorkedHoursReportQuery $query): GetWorkedHoursReportResponse
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: ClockInVoter::view(),
            userId: $query->activeUserId,
        );

        $employeeId = EmployeeId::create($query->employeeId);
        $startDate = new DateTimeImmutable($query->startDate);
        $endDate = new DateTimeImmutable($query->endDate);

        // Obtener fichajes del periodo
        $clockIns = $this->clockInRepository->findByEmployeeAndDateRange(
            employeeId: $employeeId,
            startDate: $startDate,
            endDate: $endDate,
        );

        // Calcular horas trabajadas
        $workedResult = $this->calculator->calculateWorkedHours($clockIns);

        // Comparar con planificación si se solicita
        $comparisonResult = null;
        if ($query->compareWithPlanification) {
            $employee = $this->employeeRepository->findById($employeeId);

            if ($employee !== null && $employee->hasPlanification()) {
                $comparisonResult = $this->calculator->compareWithPlanification(
                    clockIns: $clockIns,
                    planification: $employee->planification(),
                    startDate: $startDate,
                    endDate: $endDate,
                );
            }
        }

        return new GetWorkedHoursReportResponse(
            employeeId: $query->employeeId,
            startDate: $query->startDate,
            endDate: $query->endDate,
            workedHours: $workedResult->toArray(),
            comparison: $comparisonResult?->toArray(),
        );
    }
}
