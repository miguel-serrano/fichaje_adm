<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use App\DDD\Shared\Domain\Event\DomainEvent;

/**
 * Enriquece eventos de dominio con metadata contextual
 */
final class EventEnricher
{
    /** @var callable[] */
    private array $customEnrichers = [];

    public function __construct(
        private readonly CorrelationIdProvider $correlationIdProvider,
        private readonly UserContextProvider $userContextProvider,
        private readonly RequestContextProvider $requestContextProvider,
        private readonly ?GeoIpProvider $geoIpProvider = null,
    ) {}

    /**
     * Enriquece un evento con toda la metadata disponible
     */
    public function enrich(DomainEvent $event): array
    {
        $metadata = [
            // Trazabilidad
            'correlation_id' => $this->correlationIdProvider->get(),
            'causation_id' => $this->correlationIdProvider->getCausationId(),
            'enriched_at' => (new \DateTimeImmutable())->format('Y-m-d\TH:i:s.uP'),

            // Contextos
            'user' => $this->userContextProvider->getContext(),
            'request' => $this->requestContextProvider->getContext(),
            'geo' => $this->getGeoContext(),
            'system' => $this->getSystemContext(),

            // Info del evento
            'event_meta' => [
                'class' => get_class($event),
                'version' => $this->getEventVersion($event),
            ],
        ];

        // Enrichers personalizados
        foreach ($this->customEnrichers as $enricher) {
            $customData = $enricher($event, $metadata);
            if (is_array($customData)) {
                $metadata = array_merge($metadata, $customData);
            }
        }

        return $this->filterNulls($metadata);
    }

    /**
     * Registra un enricher personalizado
     * 
     * @param callable(DomainEvent, array): ?array $enricher
     */
    public function registerEnricher(callable $enricher): void
    {
        $this->customEnrichers[] = $enricher;
    }

    private function getGeoContext(): ?array
    {
        if ($this->geoIpProvider === null) {
            return null;
        }

        $ip = request()?->ip();

        if ($ip === null) {
            return null;
        }

        return $this->geoIpProvider->lookup($ip);
    }

    private function getSystemContext(): array
    {
        return [
            'environment' => app()->environment(),
            'hostname' => gethostname(),
            'app_version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'memory' => [
                'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ],
        ];
    }

    private function getEventVersion(DomainEvent $event): int
    {
        if (method_exists($event, 'version')) {
            return $event->version();
        }

        return 1;
    }

    private function filterNulls(array $data): array
    {
        return array_filter($data, fn($v) => $v !== null);
    }
}
