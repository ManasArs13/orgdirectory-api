<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ActivityResource",
 *     type="object",
 *     description="Ресурс вида деятельности",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID вида деятельности"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Мясная продукция",
 *         description="Название вида деятельности"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         nullable=true,
 *         example=5,
 *         description="ID родительской категории (null для корневых категорий)"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer",
 *         nullable=true,
 *         example=2,
 *         description="Уровень вложенности (если включено вычисление)"
 *     ),
 *     @OA\Property(
 *         property="organizations_count",
 *         type="integer",
 *         nullable=true,
 *         example=15,
 *         description="Количество связанных организаций (если включен подсчет)"
 *     ),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         nullable=true,
 *         description="Дочерние категории (если включена загрузка)",
 *         @OA\Items(ref="#/components/schemas/ActivityResource")
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-01-01 12:00:00",
 *         description="Дата создания"
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
class ActivityResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'level' => $this->whenAppended('level'), // Если добавили вычисляемое поле
            'organizations_count' => $this->whenCounted('organizations'),
            'children' => ActivityResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
