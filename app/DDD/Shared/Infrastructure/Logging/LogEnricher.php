<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Logging;

use Illuminate\Log\LogManager;
use Monolog\Processor\ProcessorInterface;

/**
 * Enriquece todos los logs con contexto adicional
 *
 * Registrar en AppServiceProvider:
 * $this->app->resolving(LogManager::class, function ($log) {
 *     LogEnricher::register($log);
 * });
 */
final class LogEnricher implements ProcessorInterface
{
    private static bool $registered = false;

    public static function register(LogManager $logManager): void
    {
        if (self::$registered) {
            return;
        }

        foreach ($logManager->getChannels() as $channel) {
            if (method_exists($channel, 'pushProcessor')) {
                $channel->pushProcessor(new self());
            }
        }

        self::$registered = true;
    }

    public function __invoke(array $record): array
    {

        $record['extra'] = array_merge(
            $record['extra'] ?? [],
            $this->getEnrichedContext()
        );

        return $record;
    }

    private function getEnrichedContext(): array
    {
        return [

            'correlation_id' => $this->getCorrelationId(),
            'request_id' => $this->getRequestId(),

            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,

            'request' => $this->getRequestContext(),

            'system' => $this->getSystemContext(),

            'timestamp_ms' => (int) (microtime(true) * 1000),
        ];
    }

    private function getCorrelationId(): ?string
    {
        return request()?->header('X-Correlation-ID')
            ?? request()?->header('X-Request-ID');
    }

    private function getRequestId(): ?string
    {
        return request()?->header('X-Request-ID');
    }

    private function getRequestContext(): ?array
    {
        $request = request();

        if ($request === null) {
            return null;
        }

        return [
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'route' => $request->route()?->getName(),
        ];
    }

    private function getSystemContext(): array
    {
        return [
            'environment' => app()->environment(),
            'hostname' => gethostname(),
            'pid' => getmypid(),
            'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        ];
    }
}
