<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Specification;

/**
 * Specification pattern para encapsular criterios de búsqueda
 */
interface Specification
{
    /**
     * Verifica si un candidato cumple la especificación
     */
    public function isSatisfiedBy(mixed $candidate): bool;
}
