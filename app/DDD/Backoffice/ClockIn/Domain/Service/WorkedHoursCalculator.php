<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Service;

use App\DDD\Backoffice\ClockIn\Domain\ClockIn;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInType;
use App\DDD\Backoffice\Employee\Domain\Entity\EmployeePlanification;
use DateTimeImmutable;

/**
 * Servicio de dominio que calcula las horas trabajadas a partir de fichajes
 * y las compara con la planificación esperada (Opción B)
 */
final class WorkedHoursCalculator
{
    /**
     * Calcula las horas trabajadas a partir de una lista de fichajes
     *
     * @param ClockIn[] $clockIns
     */
    public function calculateWorkedHours(array $clockIns): WorkedHoursResult
    {
        $totalMinutes = 0;
        $breakMinutes = 0;
        $dailyHours = [];
        $incompleteEntries = [];

        $byDate = $this->groupByDate($clockIns);

        foreach ($byDate as $date => $dayClockIns) {
            $dayResult = $this->calculateDayHours($dayClockIns);
            $dailyHours[$date] = $dayResult;
            $totalMinutes += $dayResult->workedMinutes;
            $breakMinutes += $dayResult->breakMinutes;

            if ($dayResult->hasIncompleteEntry) {
                $incompleteEntries[] = $date;
            }
        }

        return new WorkedHoursResult(
            totalMinutes: $totalMinutes,
            breakMinutes: $breakMinutes,
            netWorkedMinutes: $totalMinutes - $breakMinutes,
            dailyHours: $dailyHours,
            incompleteEntries: $incompleteEntries,
        );
    }

    /**
     * Compara las horas trabajadas con la planificación
     *
     * @param ClockIn[] $clockIns
     */
    public function compareWithPlanification(
        array $clockIns,
        EmployeePlanification $planification,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
    ): PlanificationComparisonResult {
        $workedResult = $this->calculateWorkedHours($clockIns);
        $expectedMinutes = $this->calculateExpectedMinutes($planification, $startDate, $endDate);

        $lateArrivals = [];
        $earlyDepartures = [];
        $missedDays = [];

        $current = $startDate;
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $dayOfWeek = (int) $current->format('N');

            $expectedSchedule = $planification->getScheduleForDay($dayOfWeek);
            $dayWorked = $workedResult->dailyHours[$dateStr] ?? null;

            if ($expectedSchedule !== null && !$expectedSchedule->isDayOff()) {
                if ($dayWorked === null) {

                    $missedDays[] = $dateStr;
                } else {

                    $expectedStart = $expectedSchedule->startTime();
                    if ($expectedStart !== null && $dayWorked->firstEntry !== null) {
                        if ($dayWorked->firstEntry > $expectedStart) {
                            $lateArrivals[] = new LateArrival(
                                date: $dateStr,
                                expectedTime: $expectedStart,
                                actualTime: $dayWorked->firstEntry,
                                minutesLate: $this->minutesDifference($expectedStart, $dayWorked->firstEntry),
                            );
                        }
                    }

                    $expectedEnd = $expectedSchedule->endTime();
                    if ($expectedEnd !== null && $dayWorked->lastExit !== null) {
                        if ($dayWorked->lastExit < $expectedEnd) {
                            $earlyDepartures[] = new EarlyDeparture(
                                date: $dateStr,
                                expectedTime: $expectedEnd,
                                actualTime: $dayWorked->lastExit,
                                minutesEarly: $this->minutesDifference($dayWorked->lastExit, $expectedEnd),
                            );
                        }
                    }
                }
            }

            $current = $current->modify('+1 day');
        }

        $differenceMinutes = $workedResult->netWorkedMinutes - $expectedMinutes;

        return new PlanificationComparisonResult(
            workedMinutes: $workedResult->netWorkedMinutes,
            expectedMinutes: $expectedMinutes,
            differenceMinutes: $differenceMinutes,
            lateArrivals: $lateArrivals,
            earlyDepartures: $earlyDepartures,
            missedDays: $missedDays,
            hasOvertime: $differenceMinutes > 0,
            hasUndertime: $differenceMinutes < 0,
        );
    }

    /**
     * @param ClockIn[] $clockIns
     * @return array<string, ClockIn[]>
     */
    private function groupByDate(array $clockIns): array
    {
        $grouped = [];

        foreach ($clockIns as $clockIn) {
            $date = $clockIn->timestamp()->toDateString();
            $grouped[$date][] = $clockIn;
        }

        foreach ($grouped as $date => $dayClockIns) {
            usort($dayClockIns, fn (ClockIn $a, ClockIn $b) =>
                $a->timestamp()->value() <=> $b->timestamp()->value()
            );
            $grouped[$date] = $dayClockIns;
        }

        return $grouped;
    }

    /**
     * @param ClockIn[] $dayClockIns
     */
    private function calculateDayHours(array $dayClockIns): DayWorkedHours
    {
        $workedMinutes = 0;
        $breakMinutes = 0;
        $firstEntry = null;
        $lastExit = null;
        $hasIncompleteEntry = false;

        $entryTime = null;
        $breakStartTime = null;

        foreach ($dayClockIns as $clockIn) {
            $time = $clockIn->timestamp()->toTimeString();
            $type = $clockIn->type();

            if ($type === ClockInType::ENTRY) {
                $entryTime = $clockIn->timestamp()->value();
                if ($firstEntry === null) {
                    $firstEntry = $time;
                }
            } elseif ($type === ClockInType::EXIT && $entryTime !== null) {
                $exitTime = $clockIn->timestamp()->value();
                $workedMinutes += ($exitTime->getTimestamp() - $entryTime->getTimestamp()) / 60;
                $lastExit = $time;
                $entryTime = null;
            } elseif ($type === ClockInType::BREAK_START) {
                $breakStartTime = $clockIn->timestamp()->value();
            } elseif ($type === ClockInType::BREAK_END && $breakStartTime !== null) {
                $breakEndTime = $clockIn->timestamp()->value();
                $breakMinutes += ($breakEndTime->getTimestamp() - $breakStartTime->getTimestamp()) / 60;
                $breakStartTime = null;
            }
        }

        if ($entryTime !== null) {
            $hasIncompleteEntry = true;
        }

        return new DayWorkedHours(
            workedMinutes: (int) $workedMinutes,
            breakMinutes: (int) $breakMinutes,
            netMinutes: (int) ($workedMinutes - $breakMinutes),
            firstEntry: $firstEntry,
            lastExit: $lastExit,
            hasIncompleteEntry: $hasIncompleteEntry,
        );
    }

    private function calculateExpectedMinutes(
        EmployeePlanification $planification,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
    ): int {
        $totalMinutes = 0;
        $current = $startDate;

        while ($current <= $endDate) {
            $dayOfWeek = (int) $current->format('N');
            $expectedHours = $planification->expectedHoursForDay($dayOfWeek);
            $totalMinutes += (int) ($expectedHours * 60);
            $current = $current->modify('+1 day');
        }

        return $totalMinutes;
    }

    private function minutesDifference(string $time1, string $time2): int
    {
        $t1 = DateTimeImmutable::createFromFormat('H:i:s', $time1);
        $t2 = DateTimeImmutable::createFromFormat('H:i:s', $time2);

        return (int) abs(($t2->getTimestamp() - $t1->getTimestamp()) / 60);
    }
}
