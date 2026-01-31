<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use Illuminate\Support\Str;

/**
 * Provee y gestiona Correlation IDs para trazabilidad
 */
final class CorrelationIdProvider
{
    private static ?string $correlationId = null;
    private static ?string $causationId = null;

    /**
     * Obtiene el Correlation ID actual (o genera uno nuevo)
     */
    public function get(): string
    {
        if (self::$correlationId === null) {
            self::$correlationId = request()?->header('X-Correlation-ID')
                ?? request()?->header('X-Request-ID')
                ?? $this->generate();
        }

        return self::$correlationId;
    }

    /**
     * Obtiene el Causation ID (evento que causó el actual)
     */
    public function getCausationId(): ?string
    {
        return self::$causationId;
    }

    /**
     * Establece el Correlation ID manualmente
     */
    public function set(string $id): void
    {
        self::$correlationId = $id;
    }

    /**
     * Establece el Causation ID
     */
    public function setCausationId(string $id): void
    {
        self::$causationId = $id;
    }

    /**
     * Genera un nuevo Correlation ID único
     */
    public function generate(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Reset (útil para tests o jobs en queue)
     */
    public function reset(): void
    {
        self::$correlationId = null;
        self::$causationId = null;
    }
}
