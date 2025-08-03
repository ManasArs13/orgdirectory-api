<?php

namespace App\Http\Controllers\Api;

use App\Actions\FindOrganizationsByActivityAction;
use App\Actions\FindOrganizationsNearbyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\NearbyOrganizationsRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Models\Building;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Контроллер для работы с организациями
 * 
 * Предоставляет методы для получения организаций по различным критериям:
 * - по зданию
 * - по виду деятельности
 * - в географической области
 * - поиск по дереву видов деятельности
 * - поиск по названию
 */
class OrganizationController extends Controller
{
    /**
     * Получить организации в конкретном здании
     * 
     * @param Building $building Модель здания
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function byBuilding(Building $building)
    {
        $organizations = $building->organizations()
            ->with(['activities', 'phones'])
            ->get();

        return OrganizationResource::collection($organizations);
    }

    /**
     * Получить организации по виду деятельности
     * 
     * @param Activity $activity Модель вида деятельности
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function byActivity(Activity $activity)
    {
        $organizations = $activity->organizations()
            ->with(['building', 'activities', 'phones'])
            ->get();

        return OrganizationResource::collection($organizations);
    }

    /**
     * Получить организации в географической области
     * 
     * Поддерживает два режима поиска:
     * 1. По радиусу (круговой области)
     * 2. По прямоугольной области (границы)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * 
     * @throws \Illuminate\Validation\ValidationException
     * 
     * @queryParam lat required Широта центра поиска (для радиуса)
     * @queryParam lng required Долгота центра поиска (для радиуса)
     * @queryParam radius Радиус поиска в километрах (по умолчанию 1)
     * @queryParam ne_lat Северо-восточная широта (для прямоугольной области)
     * @queryParam ne_lng Северо-восточная долгота (для прямоугольной области)
     * @queryParam sw_lat Юго-западная широта (для прямоугольной области)
     * @queryParam sw_lng Юго-западная долгота (для прямоугольной области)
     */
    public function nearby(NearbyOrganizationsRequest $request, FindOrganizationsNearbyAction $action)
    {
        $bounds = $request->has(['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng'])
            ? $request->only(['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng'])
            : null;

        $organizations = $action->execute(
            $request->lat,
            $request->lng,
            $request->radius,
            $bounds
        );

        return OrganizationResource::collection($organizations);
    }

    /**
     * Получить полную информацию об организации
     * 
     * @param Organization $organization Модель организации
     * @return JsonResponse
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "name": "Название организации",
     *     "building": {...},
     *     "activities": [...],
     *     "phones": [...]
     *   }
     * }
     */
    public function show(Organization $organization)
    {
        return new OrganizationResource($organization);
    }

    /**
     * Поиск организаций по дереву видов деятельности
     * 
     * Возвращает организации, связанные с указанным видом деятельности
     * или любым из его подчиненных видов (дочерних в иерархии)
     * 
     * @param Activity $activity Модель вида деятельности (корень дерева)
     * @return JsonResponse
     * 
     * @response {
     *   "data": [...]
     * }
     */
    public function searchByActivityTree(Activity $activity, FindOrganizationsByActivityAction $action)
    {
        $organizations = $action->execute($activity);

        return OrganizationResource::collection($organizations);
    }

    /**
     * Поиск организаций по названию
     * 
     * @param string $name Часть названия для поиска
     * @return JsonResponse
     * 
     * @response {
     *   "data": [...]
     * }
     */
    public function searchByName($name)
    {
        $organizations = Organization::where('name', 'like', "%{$name}%")
            ->get();

        return OrganizationResource::collection($organizations);
    }
}
