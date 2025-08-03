<?php

namespace App\Actions;

use App\Models\Organization;

/**
 * Сервисный класс для поиска организаций в географической области
 *
 * Обеспечивает два режима поиска:
 * 1. По радиусу от центральной точки (круговая область)
 * 2. По прямоугольной области (границы)
 */
class FindOrganizationsNearbyAction
{
    /**
     * Выполняет поиск организаций в заданной географической области
     *
     * @param float|null $lat Широта центра поиска (для радиуса)
     * @param float|null $lng Долгота центра поиска (для радиуса)
     * @param float|null $radius Радиус поиска в километрах (для радиуса)
     * @param array|null $bounds Границы прямоугольной области в формате:
     *               [
     *                 'ne_lat' => северо-восточная широта,
     *                 'ne_lng' => северо-восточная долгота,
     *                 'sw_lat' => юго-западная широта,
     *                 'sw_lng' => юго-западная долгота
     *               ]
     * @return \Illuminate\Database\Eloquent\Collection Коллекция найденных организаций
     *
     * @throws \InvalidArgumentException Если не переданы параметры для поиска
     *
     * @example Поиск по радиусу
     * $action->execute(55.7558, 37.6176, 1.5);
     *
     * @example Поиск по прямоугольной области
     * $action->execute(null, null, null, [
     *     'ne_lat' => 55.7600, 'ne_lng' => 37.6200,
     *     'sw_lat' => 55.7500, 'sw_lng' => 37.6100
     * ]);
     */
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
