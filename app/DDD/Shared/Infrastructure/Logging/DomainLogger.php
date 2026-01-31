<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Logging;

/**
 * Logger específico para eventos de dominio
 */
final class DomainLogger
{
    public static function clockInCreated(
        int $clockInId,
        int $employeeId,
        string $type,
        ?int $workplaceId = null,
    ): void {
        LogContext::create()
            ->withClockIn($clockInId)
            ->withEmployee($employeeId)
            ->with('type', $type)
            ->withWorkplace($workplaceId ?? 0)
            ->info('ClockIn created');
    }

    public static function lateArrivalDetected(
        int $employeeId,
        string $employeeName,
        int $minutesLate,
    ): void {
        LogContext::create()
            ->withEmployee($employeeId)
            ->with('employee_name', $employeeName)
            ->with('minutes_late', $minutesLate)
            ->warning('Late arrival detected');
    }

    public static function geofenceViolation(
        int $employeeId,
        int $workplaceId,
        float $distance,
        int $allowedRadius,
    ): void {
        LogContext::create()
            ->withEmployee($employeeId)
            ->withWorkplace($workplaceId)
            ->with('distance', $distance)
            ->with('allowed_radius', $allowedRadius)
            ->warning('Geofence violation detected');
    }

    public static function employeeCreated(
        int $employeeId,
        string $email,
    ): void {
        LogContext::create()
            ->withEmployee($employeeId)
            ->with('email', $email)
            ->info('Employee created');
    }

    public static function accessDenied(
        int $userId,
        string $permission,
        string $resource,
    ): void {
        LogContext::create()
            ->withUser($userId)
            ->with('permission', $permission)
            ->with('resource', $resource)
            ->warning('Access denied');
    }

    public static function eventPublished(
        string $eventName,
        string $aggregateId,
    ): void {
        LogContext::create()
            ->with('event_name', $eventName)
            ->with('aggregate_id', $aggregateId)
            ->debug('Domain event published');
    }

    public static function eventSyncedToElasticsearch(
        string $eventId,
        string $eventName,
    ): void {
        LogContext::create()
            ->with('event_id', $eventId)
            ->with('event_name', $eventName)
            ->debug('Event synced to Elasticsearch');
    }

    public static function eventSyncFailed(
        string $eventId,
        \Throwable $error,
    ): void {
        LogContext::create()
            ->with('event_id', $eventId)
            ->withError($error)
            ->error('Event sync to Elasticsearch failed');
    }
}
