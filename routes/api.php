<?php

use App\Http\Controllers\Api\OrganizationController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Маршруты для API версии 1. Все маршруты имеют префикс api
| и префикс именования api.
|
*/

Route::name('api.')->group(function () {
    // Получить организации в здании
    Route::get('/buildings/{building}/organizations', [OrganizationController::class, 'byBuilding']);

    // Получить организации по виду деятельности
    Route::get('/activities/{activity}/organizations', [OrganizationController::class, 'byActivity']);

    // Поиск организаций в радиусе/области
    Route::get('/organizations/nearby', [OrganizationController::class, 'nearby']);

    // Получить информацию об организации
    Route::get('/organizations/{organization}', [OrganizationController::class, 'show']);

    // Поиск по дереву видов деятельности
    Route::get('/organizations/search/activity/{activity}', [OrganizationController::class, 'searchByActivityTree']);

    // Поиск организаций по названию
    Route::get('/organizations/search/name/{name}', [OrganizationController::class, 'searchByName']);
});
