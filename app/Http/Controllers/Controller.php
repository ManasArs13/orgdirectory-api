<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Справочник организаций API" 
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="api_key",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-KEY"
 * ),
 * @OA\PathItem(path="/api/")
 */
abstract class Controller
{
    //
}
