<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Provee geolocalización por IP
 *
 * Soporta múltiples proveedores:
 * - ip-api.com (gratis, limitado)
 * - MaxMind GeoIP2 (de pago, más preciso)
 */
final class GeoIpProvider
{
    private const CACHE_TTL = 86400;

    public function __construct(
        private readonly string $provider = 'ip-api',
        private readonly ?string $maxMindLicenseKey = null,
    ) {}

    public function lookup(string $ip): ?array
    {
        if ($this->isPrivateIp($ip)) {
            return null;
        }

        $cacheKey = "geoip:{$ip}";
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $result = match ($this->provider) {
            'maxmind' => $this->lookupMaxMind($ip),
            default => $this->lookupIpApi($ip),
        };

        if ($result !== null) {
            Cache::put($cacheKey, $result, self::CACHE_TTL);
        }

        return $result;
    }

    /**
     * Usa ip-api.com (gratis, 45 req/min)
     */
    private function lookupIpApi(string $ip): ?array
    {
        try {
            $response = Http::timeout(2)
                ->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org',
                ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if ($data['status'] !== 'success') {
                return null;
            }

            return [
                'country' => $data['country'] ?? null,
                'country_code' => $data['countryCode'] ?? null,
                'region' => $data['regionName'] ?? null,
                'region_code' => $data['region'] ?? null,
                'city' => $data['city'] ?? null,
                'postal_code' => $data['zip'] ?? null,
                'latitude' => $data['lat'] ?? null,
                'longitude' => $data['lon'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'isp' => $data['isp'] ?? null,
                'organization' => $data['org'] ?? null,
                'provider' => 'ip-api',
            ];
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Usa MaxMind GeoIP2 (requiere licencia)
     */
    private function lookupMaxMind(string $ip): ?array
    {
        if ($this->maxMindLicenseKey === null) {
            return $this->lookupIpApi($ip);
        }

        try {
            $response = Http::timeout(2)
                ->withBasicAuth($this->maxMindLicenseKey, '')
                ->get("https://geoip.maxmind.com/geoip/v2.1/city/{$ip}");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'country' => $data['country']['names']['en'] ?? null,
                'country_code' => $data['country']['iso_code'] ?? null,
                'region' => $data['subdivisions'][0]['names']['en'] ?? null,
                'region_code' => $data['subdivisions'][0]['iso_code'] ?? null,
                'city' => $data['city']['names']['en'] ?? null,
                'postal_code' => $data['postal']['code'] ?? null,
                'latitude' => $data['location']['latitude'] ?? null,
                'longitude' => $data['location']['longitude'] ?? null,
                'timezone' => $data['location']['time_zone'] ?? null,
                'accuracy_radius' => $data['location']['accuracy_radius'] ?? null,
                'provider' => 'maxmind',
            ];
        } catch (\Throwable $e) {
            report($e);
            return $this->lookupIpApi($ip);
        }
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
