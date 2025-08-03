<?php

namespace Tests\Feature;

use App\Actions\FindOrganizationsNearbyAction;
use App\Http\Middleware\CheckStaticKey;
use App\Http\Middleware\VerifyApiKey;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NearbyOrganizationsTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultLat = 55.755826;
    protected $defaultLng = 37.617300;

    #[Test]
    public function it_finds_organizations_within_radius()
    {
        $mock = $this->mock(FindOrganizationsNearbyAction::class);
        $mock->shouldReceive('execute')
            ->andReturn(Organization::factory()->count(2)->make());

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson('/api/organizations/nearby?lat=55.75&lng=37.61');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_finds_organizations_in_bounding_box()
    {
        $building1 = Building::factory()->create([
            'latitude' => 55.75,
            'longitude' => 37.60
        ]);

        $building2 = Building::factory()->create([
            'latitude' => 55.80,
            'longitude' => 37.70
        ]);

        Organization::factory()->for($building1)->create(['name' => 'Inside Organization']);
        Organization::factory()->for($building2)->create(['name' => 'Outside Organization']);

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/organizations/nearby?" . http_build_query([
                'ne_lat' => 55.76,
                'ne_lng' => 37.65,
                'sw_lat' => 55.74,
                'sw_lng' => 37.55
            ]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Inside Organization'])
            ->assertJsonMissing(['name' => 'Outside Organization']);
    }
}
