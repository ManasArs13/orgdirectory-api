<?php

namespace App\Actions;

use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class FindOrganizationsNearbyAction
{
    public function execute(?float $lat = null, ?float $lng = null, ?float $radius = null, ?array $bounds = null)
    {
        $query = Organization::query();

        if ($bounds) {
            // Поиск в прямоугольной области
            $query->whereHas('building', function ($q) use ($bounds) {
                $q->whereBetween('latitude', [$bounds['sw_lat'], $bounds['ne_lat']])
                    ->whereBetween('longitude', [$bounds['sw_lng'], $bounds['ne_lng']]);
            });
        } else {
            // Поиск в радиусе
            $query->whereHas('building', function ($q) use ($lat, $lng, $radius) {
                $q->select('*')
                    ->selectRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                      cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                      sin(radians(latitude)))) AS distance',
                        [$lat, $lng, $lat]
                    )
                    ->having('distance', '<', $radius)
                    ->orderBy('distance');
            });
        }

        return $query->get();
    }
}
