<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Quartier Rosiers 3e Programme Rive Gauche : centre et nom dans config/quartier.php,
 * rayon modifiable depuis l'application (table settings), config en valeur par defaut.
 */
class Quartier
{
    public const RADIUS_SETTING = 'quartier.radius_km';

    public static function name(): string
    {
        return (string) config('quartier.name');
    }

    public static function center(): array
    {
        return [(float) config('quartier.latitude'), (float) config('quartier.longitude')];
    }

    public static function radiusKm(): float
    {
        return (float) Setting::get(self::RADIUS_SETTING, config('quartier.radius_km'));
    }

    public static function contains(float $latitude, float $longitude): bool
    {
        [$centerLat, $centerLng] = self::center();

        return self::distanceKm($latitude, $longitude, $centerLat, $centerLng) <= self::radiusKm();
    }

    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $h = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * asin(sqrt($h));
    }
}
