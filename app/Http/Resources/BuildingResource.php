<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BuildingResource",
 *     type="object",
 *     description="Ресурс здания",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID здания"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         example="г. Москва, ул. Ленина, 1",
 *         description="Полный адрес здания"
 *     ),
 *     @OA\Property(
 *         property="coordinates",
 *         type="object",
 *         description="Географические координаты",
 *         @OA\Property(
 *             property="latitude",
 *             type="number",
 *             format="float",
 *             example=55.755826,
 *             description="Широта"
 *         ),
 *         @OA\Property(
 *             property="longitude",
 *             type="number",
 *             format="float",
 *             example=37.617300,
 *             description="Долгота"
 *         )
 *     ),
 *     @OA\Property(
 *         property="organizations_count",
 *         type="integer",
 *         nullable=true,
 *         example=5,
 *         description="Количество организаций в здании (если включен подсчет)"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-01-01 12:00:00",
 *         description="Дата создания записи"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-01-01 12:00:00",
 *         description="Дата последнего обновления"
 *     )
 * )
 */
class BuildingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'coordinates' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'organizations_count' => $this->whenCounted('organizations'),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
