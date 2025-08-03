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

class ShowOrganizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_organization_details()
    {
        $building = Building::factory()->create([
            'address' => 'ул. Ленина, 1',
            'latitude' => 55.755826,
            'longitude' => 37.617300
        ]);

        $activity = Activity::factory()->create(['name' => 'IT услуги']);

        $organization = Organization::factory()
            ->for($building)
            ->create([
                'name' => 'ООО Технологии'
            ]);

        $organization->activities()->attach($activity);

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/organizations/{$organization->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'phones',
                    'building' => [
                        'id',
                        'address',
                        'coordinates' => [
                            'latitude',
                            'longitude'
                        ]
                    ],
                    'activities' => [
                        '*' => [
                            'id',
                            'name'
                        ]
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_organization()
    {
        $nonExistentId = 9999;

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/organizations/{$nonExistentId}");

        $response->assertStatus(404);
    }
}
