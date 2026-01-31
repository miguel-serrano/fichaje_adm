<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Logging;

use Illuminate\Support\Facades\Log;

/**
 * Helper para logging estructurado
 */
final class LogContext
{
    private array $context = [];

    public static function create(): self
    {
        return new self();
    }

    public function withUser(?int $userId): self
    {
        $this->context['user_id'] = $userId;
        return $this;
    }

    public function withEmployee(int $employeeId): self
    {
        $this->context['employee_id'] = $employeeId;
        return $this;
    }

    public function withWorkplace(int $workplaceId): self
    {
        $this->context['workplace_id'] = $workplaceId;
        return $this;
    }

    public function withClockIn(int $clockInId): self
    {
        $this->context['clock_in_id'] = $clockInId;
        return $this;
    }

    public function withRequest(array $request): self
    {
        $this->context['request'] = $request;
        return $this;
    }

    public function withDuration(float $milliseconds): self
    {
        $this->context['duration_ms'] = $milliseconds;
        return $this;
    }

    public function withError(\Throwable $e): self
    {
        $this->context['error'] = [
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];
        return $this;
    }

    public function with(string $key, mixed $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }

    public function info(string $message): void
    {
        Log::info($message, $this->buildContext());
    }

    public function warning(string $message): void
    {
        Log::warning($message, $this->buildContext());
    }

    public function error(string $message): void
    {
        Log::error($message, $this->buildContext());
    }

    public function debug(string $message): void
    {
        Log::debug($message, $this->buildContext());
    }

    private function buildContext(): array
    {
        return array_merge($this->context, [
            'timestamp' => now()->toIso8601String(),
            'request_id' => request()?->header('X-Request-ID'),
            'ip' => request()?->ip(),
        ]);
    }
}
