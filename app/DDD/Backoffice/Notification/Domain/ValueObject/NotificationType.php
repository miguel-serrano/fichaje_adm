<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain\ValueObject;

enum NotificationType: string
{

    case CLOCK_IN_LATE = 'clock_in_late';
    case CLOCK_IN_EARLY_DEPARTURE = 'clock_in_early_departure';
    case CLOCK_IN_MISSED = 'clock_in_missed';
    case CLOCK_IN_GEOFENCE_VIOLATION = 'clock_in_geofence_violation';

    case EMPLOYEE_CONTRACT_EXPIRING = 'employee_contract_expiring';
    case EMPLOYEE_CREATED = 'employee_created';

    case SYSTEM_ALERT = 'system_alert';
    case SYSTEM_INFO = 'system_info';

    public function label(): string
    {
        return match ($this) {
            self::CLOCK_IN_LATE => 'Retraso en fichaje',
            self::CLOCK_IN_EARLY_DEPARTURE => 'Salida anticipada',
            self::CLOCK_IN_MISSED => 'Fichaje no realizado',
            self::CLOCK_IN_GEOFENCE_VIOLATION => 'Fichaje fuera de zona',
            self::EMPLOYEE_CONTRACT_EXPIRING => 'Contrato próximo a vencer',
            self::EMPLOYEE_CREATED => 'Nuevo empleado',
            self::SYSTEM_ALERT => 'Alerta del sistema',
            self::SYSTEM_INFO => 'Información del sistema',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CLOCK_IN_LATE, self::CLOCK_IN_EARLY_DEPARTURE, self::CLOCK_IN_MISSED => 'clock',
            self::CLOCK_IN_GEOFENCE_VIOLATION => 'location',
            self::EMPLOYEE_CONTRACT_EXPIRING, self::EMPLOYEE_CREATED => 'user',
            self::SYSTEM_ALERT => 'warning',
            self::SYSTEM_INFO => 'info',
        };
    }

    public function severity(): string
    {
        return match ($this) {
            self::CLOCK_IN_LATE, self::CLOCK_IN_EARLY_DEPARTURE => 'warning',
            self::CLOCK_IN_MISSED, self::CLOCK_IN_GEOFENCE_VIOLATION => 'danger',
            self::EMPLOYEE_CONTRACT_EXPIRING => 'warning',
            self::EMPLOYEE_CREATED, self::SYSTEM_INFO => 'info',
            self::SYSTEM_ALERT => 'danger',
        };
    }
}
