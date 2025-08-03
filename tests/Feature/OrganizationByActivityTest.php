<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckStaticKey;
use App\Http\Middleware\VerifyApiKey;
use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrganizationByActivityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_organizations_for_specific_activity()
    {
        // Создаем тестовые данные
        $activity = Activity::factory()->withChildren()->create();

        Building::factory()
            ->has(
                $organization = Organization::factory()
                    ->hasAttached(
                        $activity
                    )
                    ->withActivities(1)
                    ->withPhones(rand(1, 3)),
                'organizations'
            )
            ->create();

        // Выполняем запрос
        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/activities/{$activity->id}/organizations");

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'phones',
                        'building',
                        'activities',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function it_returns_empty_list_when_no_organizations_for_activity()
    {
        $activity = Activity::factory()->create();

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/activities/{$activity->id}/organizations");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_returns_404_for_nonexistent_activity()
    {
        $nonExistentId = 9999;

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/activities/{$nonExistentId}/organizations");

        $response->assertStatus(404);
    }
}
