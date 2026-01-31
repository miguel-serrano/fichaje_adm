<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain\Service;

use App\DDD\Backoffice\Notification\Domain\Interface\NotificationRepositoryInterface;
use App\DDD\Backoffice\Notification\Domain\Notification;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationBody;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationChannel;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationData;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationRecipientId;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationTitle;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationType;

/**
 * Servicio de dominio para crear y enviar notificaciones
 */
final class Notifier
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
    ) {}

    public function notify(
        NotificationType $type,
        string $title,
        string $body,
        int $recipientId,
        NotificationChannel $channel = NotificationChannel::DATABASE,
        ?array $data = null,
    ): Notification {
        $notification = Notification::create(
            id: $this->repository->nextId(),
            type: $type,
            title: NotificationTitle::create($title),
            body: NotificationBody::create($body),
            channel: $channel,
            recipientId: NotificationRecipientId::create($recipientId),
            data: $data !== null ? NotificationData::create($data) : null,
        );

        // Para canal DATABASE, marcamos como enviada inmediatamente
        if ($channel === NotificationChannel::DATABASE) {
            $notification->markAsSent();
        }

        $this->repository->save($notification);

        return $notification;
    }

    public function notifyLateArrival(
        int $managerId,
        int $employeeId,
        string $employeeName,
        string $date,
        int $minutesLate,
    ): Notification {
        return $this->notify(
            type: NotificationType::CLOCK_IN_LATE,
            title: 'Retraso en fichaje',
            body: sprintf(
                '%s llegó %d minutos tarde el %s',
                $employeeName,
                $minutesLate,
                $date,
            ),
            recipientId: $managerId,
            data: NotificationData::forLateArrival(
                $employeeId,
                $employeeName,
                $date,
                $minutesLate,
            )->toArray(),
        );
    }

    public function notifyMissedClockIn(
        int $managerId,
        int $employeeId,
        string $employeeName,
        string $date,
    ): Notification {
        return $this->notify(
            type: NotificationType::CLOCK_IN_MISSED,
            title: 'Fichaje no realizado',
            body: sprintf(
                '%s no fichó el %s',
                $employeeName,
                $date,
            ),
            recipientId: $managerId,
            data: NotificationData::forMissedClockIn(
                $employeeId,
                $employeeName,
                $date,
            )->toArray(),
        );
    }

    public function notifyGeofenceViolation(
        int $managerId,
        int $employeeId,
        string $employeeName,
        int $workplaceId,
        string $workplaceName,
        float $distance,
        int $allowedRadius,
    ): Notification {
        return $this->notify(
            type: NotificationType::CLOCK_IN_GEOFENCE_VIOLATION,
            title: 'Fichaje fuera de zona',
            body: sprintf(
                '%s fichó a %.0fm del centro "%s" (máx: %dm)',
                $employeeName,
                $distance,
                $workplaceName,
                $allowedRadius,
            ),
            recipientId: $managerId,
            data: NotificationData::forGeofenceViolation(
                $employeeId,
                $employeeName,
                $workplaceId,
                $workplaceName,
                $distance,
                $allowedRadius,
            )->toArray(),
        );
    }

    public function notifyContractExpiring(
        int $managerId,
        int $employeeId,
        string $employeeName,
        string $expirationDate,
        int $daysRemaining,
    ): Notification {
        return $this->notify(
            type: NotificationType::EMPLOYEE_CONTRACT_EXPIRING,
            title: 'Contrato próximo a vencer',
            body: sprintf(
                'El contrato de %s vence en %d días (%s)',
                $employeeName,
                $daysRemaining,
                $expirationDate,
            ),
            recipientId: $managerId,
            data: [
                'employee_id' => $employeeId,
                'employee_name' => $employeeName,
                'expiration_date' => $expirationDate,
                'days_remaining' => $daysRemaining,
            ],
        );
    }
}
