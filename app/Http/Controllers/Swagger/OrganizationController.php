<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="Organizations",
 *     description="Операции с организациями"
 * )
 */
class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/buildings/{building}/organizations",
     *     tags={"Organizations"},
     *     summary="Организации в конкретном здании",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="building",
     *         in="path",
     *         required=true,
     *         description="ID здания",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrganizationResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неверный API ключ"
     *     )
     * )
     */
    public function byBuilding() {}

    /**
     * @OA\Get(
     *     path="/api/activities/{activity}/organizations",
     *     tags={"Organizations"},
     *     summary="Организации по виду деятельности",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="activity",
     *         in="path",
     *         required=true,
     *         description="ID вида деятельности",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrganizationResource")
     *         )
     *     )
     * )
     */
    public function byActivity() {}

    /**
     * @OA\Get(
     *     path="/api/organizations/nearby",
     *     tags={"Organizations"},
     *     summary="Организации в географической области",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         required=true,
     *         description="Широта центра",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         required=true,
     *         description="Долгота центра",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Радиус поиска в км",
     *         @OA\Schema(type="number", format="float", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="ne_lat",
     *         in="query",
     *         description="Северо-восточная широта (для прямоугольной области)",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="ne_lng",
     *         in="query",
     *         description="Северо-восточная долгота (для прямоугольной области)",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="sw_lat",
     *         in="query",
     *         description="Юго-западная широта (для прямоугольной области)",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="sw_lng",
     *         in="query",
     *         description="Юго-западная долгота (для прямоугольной области)",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrganizationResource")
     *         )
     *     )
     * )
     */
    public function nearby() {}

    /**
     * @OA\Get(
     *     path="/api/organizations/{organization}",
     *     tags={"Organizations"},
     *     summary="Информация об организации",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="organization",
     *         in="path",
     *         required=true,
     *         description="ID организации",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные организации",
     *         @OA\JsonContent(ref="#/components/schemas/OrganizationResource")
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Get(
     *     path="/api/organizations/search/activity/{activity}",
     *     tags={"Organizations"},
     *     summary="Поиск по дереву видов деятельности",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="activity",
     *         in="path",
     *         required=true,
     *         description="ID корневого вида деятельности",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrganizationResource")
     *         )
     *     )
     * )
     */
    public function searchByActivityTree() {}

    /**
     * @OA\Get(
     *     path="/api/organizations/search/name/{name}",
     *     tags={"Organizations"},
     *     summary="Поиск организаций по названию",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Часть названия для поиска",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrganizationResource")
     *         )
     *     )
     * )
     */
    public function searchByName() {}
}
