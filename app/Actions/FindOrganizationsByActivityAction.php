<?php

namespace App\Actions;

use App\Models\Organization;

/**
 * Сервисный класс для поиска организаций по дереву видов деятельности
 * 
 * Находит все организации, связанные с указанным видом деятельности
 * или любым из его подчиненных видов в иерархии (дочерних, внучатых и т.д.)
 * с учетом ограничения максимальной глубины вложенности (3 уровня)
 */
class FindOrganizationsByActivityAction
{
    /**
     * Выполняет поиск организаций по дереву видов деятельности
     *
     * @param \App\Models\Activity $activity Корневой вид деятельности для поиска
     * @return \Illuminate\Database\Eloquent\Collection Коллекция найденных организаций
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если вид деятельности не найден
     * 
     * @example
     * $action = new FindOrganizationsByActivityAction();
     * $organizations = $action->execute($activity);
     */
    public function execute($activity)
    {
        $query = Organization::with(['building', 'activities', 'phones']);

        // Получаем ID всех подкатегорий (включая текущую)
        $activityIds = $activity->getDescendantsAndSelf()->pluck('id');

        $query = Organization::whereHas('activities', function ($q) use ($activityIds) {
            $q->whereIn('activities.id', $activityIds);
        });

        return $query->get();
    }
}
