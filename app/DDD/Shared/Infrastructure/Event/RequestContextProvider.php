<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

/**
 * Provee contexto enriquecido de la request HTTP actual
 */
final class RequestContextProvider
{
    public function getContext(): ?array
    {
        $request = request();

        if ($request === null) {
            return $this->getCliContext();
        }

        return [
            'source' => 'http',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'route' => $this->getRouteInfo($request),
            'ip' => $this->getIpInfo($request),
            'user_agent' => $this->parseUserAgent($request->userAgent()),
            'headers' => $this->getSafeHeaders($request),
            'query_params' => $this->getSafeQueryParams($request),
            'timing' => $this->getTimingInfo(),
        ];
    }

    private function getCliContext(): array
    {
        global $argv;

        return [
            'source' => 'cli',
            'command' => $argv[0] ?? 'unknown',
            'arguments' => array_slice($argv ?? [], 1),
        ];
    }

    private function getRouteInfo($request): ?array
    {
        $route = $request->route();

        if ($route === null) {
            return null;
        }

        return [
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'parameters' => $route->parameters(),
            'middleware' => $route->middleware(),
        ];
    }

    private function getIpInfo($request): array
    {
        $ip = $request->ip();

        return [
            'address' => $ip,
            'forwarded_for' => $request->header('X-Forwarded-For'),
            'real_ip' => $request->header('X-Real-IP'),
            'is_trusted_proxy' => $request->isFromTrustedProxy(),
        ];
    }

    private function parseUserAgent(?string $userAgent): array
    {
        if ($userAgent === null) {
            return ['raw' => null];
        }

        return [
            'raw' => $userAgent,
            'browser' => $this->detectBrowser($userAgent),
            'os' => $this->detectOS($userAgent),
            'device' => $this->detectDevice($userAgent),
            'is_bot' => $this->isBot($userAgent),
        ];
    }

    private function detectBrowser(string $ua): string
    {
        $browsers = [
            'Edge' => '/Edg\/[\d.]+/',
            'Chrome' => '/Chrome\/[\d.]+/',
            'Firefox' => '/Firefox\/[\d.]+/',
            'Safari' => '/Safari\/[\d.]+(?!.*Chrome)/',
            'Opera' => '/Opera|OPR\//',
            'IE' => '/MSIE|Trident/',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return strtolower($name);
            }
        }

        return 'unknown';
    }

    private function detectOS(string $ua): string
    {
        $systems = [
            'windows' => '/Windows NT/',
            'macos' => '/Mac OS X/',
            'linux' => '/Linux/',
            'android' => '/Android/',
            'ios' => '/iPhone|iPad|iPod/',
        ];

        foreach ($systems as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }

        return 'unknown';
    }

    private function detectDevice(string $ua): string
    {
        if (preg_match('/Mobile|Android.*Mobile|iPhone/i', $ua)) {
            return 'mobile';
        }

        if (preg_match('/iPad|Android(?!.*Mobile)|Tablet/i', $ua)) {
            return 'tablet';
        }

        return 'desktop';
    }

    private function isBot(string $ua): bool
    {
        $botPatterns = '/bot|crawl|spider|slurp|googlebot|bingbot|yandex/i';
        return (bool) preg_match($botPatterns, $ua);
    }

    private function getSafeHeaders($request): array
    {
        $safeHeaders = [
            'accept',
            'accept-language',
            'accept-encoding',
            'content-type',
            'origin',
            'referer',
            'x-requested-with',
        ];

        $headers = [];
        foreach ($safeHeaders as $header) {
            $value = $request->header($header);
            if ($value !== null) {
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    private function getSafeQueryParams($request): array
    {
        $params = $request->query();

        // Filtrar parámetros sensibles
        $sensitiveKeys = ['password', 'token', 'secret', 'api_key', 'key'];

        return array_filter($params, function ($key) use ($sensitiveKeys) {
            return !in_array(strtolower($key), $sensitiveKeys);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getTimingInfo(): array
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : null;

        return [
            'timestamp' => now()->toIso8601String(),
            'unix_ms' => (int) (microtime(true) * 1000),
            'duration_ms' => $startTime
                ? round((microtime(true) - $startTime) * 1000, 2)
                : null,
        ];
    }
}
