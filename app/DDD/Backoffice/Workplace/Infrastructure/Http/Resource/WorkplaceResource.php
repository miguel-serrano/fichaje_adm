<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\DDD\Backoffice\Workplace\Domain\Workplace $resource
 */
final class WorkplaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primitives = $this->resource->toPrimitives();

        return [
            'id' => $primitives['id'],
            'name' => $primitives['name'],
            'address' => $primitives['address'],
            'city' => $primitives['city'],
            'postal_code' => $primitives['postal_code'],
            'is_active' => $primitives['is_active'],
            'geofence' => [
                'enabled' => $this->resource->hasGeofence(),
                'latitude' => $primitives['latitude'],
                'longitude' => $primitives['longitude'],
                'radius' => $primitives['radius'],
                'radius_formatted' => $this->formatRadius($primitives['radius']),
                'google_maps_url' => $this->getGoogleMapsUrl(
                    $primitives['latitude'],
                    $primitives['longitude']
                ),
            ],
            'full_address' => $this->getFullAddress($primitives),
            'links' => [
                'self' => route('backoffice.workplaces.show', $primitives['id']),
            ],
        ];
    }

    private function formatRadius(?int $radius): ?string
    {
        if ($radius === null) {
            return null;
        }

        if ($radius >= 1000) {
            return sprintf('%.1f km', $radius / 1000);
        }

        return sprintf('%d m', $radius);
    }

    private function getGoogleMapsUrl(?float $lat, ?float $lng): ?string
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        return sprintf(
            'https://www.google.com/maps?q=%f,%f',
            $lat,
            $lng
        );
    }

    private function getFullAddress(array $primitives): string
    {
        $parts = array_filter([
            $primitives['address'],
            $primitives['postal_code'],
            $primitives['city'],
        ]);

        return implode(', ', $parts);
    }
}
