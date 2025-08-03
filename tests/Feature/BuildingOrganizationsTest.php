<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckStaticKey;
use App\Http\Middleware\VerifyApiKey;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BuildingOrganizationsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_organizations_for_building()
    {
        $building = Building::factory()
            ->has(
                Organization::factory()
                    ->count(3)
                    ->withActivities(1),
                'organizations'
            )
            ->create();

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/buildings/{$building->id}/organizations");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'phones',
                        'building',
                        'activities' => [
                            '*' => [
                                'id',
                                'name',
                                'parent_id'
                            ]
                        ],
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');

        foreach ($response->json('data') as $org) {
            $this->assertEquals($building->id, $org['building']['id']);
        }
    }

    #[Test]
    public function it_requires_valid_api_key()
    {
        $building = Building::factory()->create();

        $response = $this->getJson("/api/buildings/{$building->id}/organizations");

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Неверный API ключ'
            ]);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_building()
    {
        $nonExistentId = 9999;

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/buildings/{$nonExistentId}/organizations");

        $response->assertStatus(404);
    }
}
