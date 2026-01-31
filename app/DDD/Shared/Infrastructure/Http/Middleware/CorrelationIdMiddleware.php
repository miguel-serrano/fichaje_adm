<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Middleware;

use App\DDD\Shared\Infrastructure\Event\CorrelationIdProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que gestiona el Correlation ID para trazabilidad end-to-end
 */
final class CorrelationIdMiddleware
{
    public function __construct(
        private readonly CorrelationIdProvider $correlationIdProvider,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Obtener o generar Correlation ID
        $correlationId = $this->correlationIdProvider->get();

        // Compartir en contexto de logs
        Log::shareContext([
            'correlation_id' => $correlationId,
            'request_id' => $correlationId, // Alias
        ]);

        // Procesar request
        $response = $next($request);

        // Añadir header a la respuesta
        $response->headers->set('X-Correlation-ID', $correlationId);
        $response->headers->set('X-Request-ID', $correlationId);

        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        // Reset para la próxima request
        $this->correlationIdProvider->reset();
    }
}
