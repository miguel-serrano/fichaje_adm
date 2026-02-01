<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Bus;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Decorator que loguea la ejecución de commands
 */
final class LoggingCommandBus implements CommandBusInterface
{
    public function __construct(
        private readonly CommandBusInterface $innerBus,
    ) {}

    public function dispatch(object $command): void
    {
        $commandClass = get_class($command);
        $startTime = microtime(true);

        Log::info('Command started', [
            'command' => $commandClass,
            'payload' => $this->extractPayload($command),
        ]);

        try {
            $this->innerBus->dispatch($command);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Command completed', [
                'command' => $commandClass,
                'duration_ms' => $duration,
            ]);
        } catch (Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('Command failed', [
                'command' => $commandClass,
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function map(array $handlers): void
    {
        $this->innerBus->map($handlers);
    }

    private function extractPayload(object $command): array
    {
        $payload = [];

        foreach ((new \ReflectionClass($command))->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($command);

            $name = $property->getName();
            if (in_array($name, ['password', 'token', 'secret', 'api_key'])) {
                $value = '***HIDDEN***';
            }

            $payload[$name] = is_object($value) ? get_class($value) : $value;
        }

        return $payload;
    }
}
