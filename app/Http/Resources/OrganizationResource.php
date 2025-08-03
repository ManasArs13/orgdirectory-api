<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrganizationResource",
 *     type="object",
 *     description="Ресурс организации",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID организации"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="ООО Рога и Копыта",
 *         description="Название организации"
 *     ),
 *     @OA\Property(
 *         property="phones",
 *         type="array",
 *         description="Телефоны организации",
 *         @OA\Items(
 *             type="string",
 *             example="+7 123 456-78-90"
 *         )
 *     ),
 *     @OA\Property(
 *         property="building",
 *         ref="#/components/schemas/BuildingResource",
 *         nullable=true,
 *         description="Здание, в котором находится организация (если включена загрузка)"
 *     ),
 *     @OA\Property(
 *         property="activities",
 *         type="array",
 *         nullable=true,
 *         description="Виды деятельности организации (если включена загрузка)",
 *         @OA\Items(ref="#/components/schemas/ActivityResource")
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
class OrganizationResource extends JsonResource
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
            'name' => $this->name,
            'phones' => $this->phones->pluck('phone'),
            'building' => new BuildingResource($this->whenLoaded('building')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
