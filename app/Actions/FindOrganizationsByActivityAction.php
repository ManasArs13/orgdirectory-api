<?php

namespace App\Actions;

use App\Models\Organization;

class FindOrganizationsByActivityAction
{
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
