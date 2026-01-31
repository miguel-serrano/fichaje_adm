<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain\ValueObject;

/**
 * Datos adicionales de la notificación (metadata)
 */
final class NotificationData
{
    private function __construct(
        private readonly array $data,
    ) {}

    public static function create(array $data): self
    {
        return new self($data);
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Crea datos para notificación de retraso
     */
    public static function forLateArrival(
        int $employeeId,
        string $employeeName,
        string $date,
        int $minutesLate,
    ): self {
        return new self([
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'date' => $date,
            'minutes_late' => $minutesLate,
        ]);
    }

    /**
     * Crea datos para notificación de fichaje perdido
     */
    public static function forMissedClockIn(
        int $employeeId,
        string $employeeName,
        string $date,
    ): self {
        return new self([
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'date' => $date,
        ]);
    }

    /**
     * Crea datos para notificación de violación de geofence
     */
    public static function forGeofenceViolation(
        int $employeeId,
        string $employeeName,
        int $workplaceId,
        string $workplaceName,
        float $distance,
        int $allowedRadius,
    ): self {
        return new self([
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'workplace_id' => $workplaceId,
            'workplace_name' => $workplaceName,
            'distance' => $distance,
            'allowed_radius' => $allowedRadius,
        ]);
    }
}
