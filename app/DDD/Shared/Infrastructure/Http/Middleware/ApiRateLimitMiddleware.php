<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiRateLimitMiddleware
{
    public function __construct(
        private readonly RateLimiter $limiter,
    ) {}

    public function handle(Request $request, Closure $next, string $limitName = 'api'): Response
    {
        $key = $this->resolveRequestSignature($request, $limitName);
        $maxAttempts = $this->getMaxAttempts($limitName);
        $decayMinutes = $this->getDecayMinutes($limitName);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildTooManyAttemptsResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addRateLimitHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    private function resolveRequestSignature(Request $request, string $limitName): string
    {
        $identifier = $request->user()?->id ?? $request->ip();

        return sprintf('%s|%s|%s', $limitName, $identifier, $request->path());
    }

    private function getMaxAttempts(string $limitName): int
    {
        return match ($limitName) {
            'api' => 60,           // 60 requests per minute
            'auth' => 5,           // 5 login attempts
            'upload' => 10,        // 10 uploads per minute
            'export' => 5,         // 5 exports per minute
            'events_sync' => 10,   // 10 syncs per minute
            default => 60,
        };
    }

    private function getDecayMinutes(string $limitName): int
    {
        return match ($limitName) {
            'auth' => 5,           // 5 minutos de bloqueo tras exceder
            'export' => 5,
            default => 1,          // 1 minuto por defecto
        };
    }

    private function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return $this->limiter->remaining($key, $maxAttempts);
    }

    private function addRateLimitHeaders(Response $response, int $maxAttempts, int $remaining): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $remaining),
        ]);

        return $response;
    }

    private function buildTooManyAttemptsResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->limiter->availableIn($key);

        return response()->json([
            'error' => 'Too Many Requests',
            'message' => 'Has excedido el límite de peticiones. Intenta de nuevo más tarde.',
            'retry_after' => $retryAfter,
        ], 429)->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'Retry-After' => $retryAfter,
        ]);
    }
}
