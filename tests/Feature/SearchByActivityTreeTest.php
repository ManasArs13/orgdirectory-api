<?php

namespace Tests\Feature;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\CheckStaticKey;
use App\Http\Middleware\VerifyApiKey;
use App\Models\Organization;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchByActivityTreeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_finds_organizations_for_activity_tree()
    {
        // Создаем дерево видов деятельности
        $rootActivity = Activity::factory()->create(['name' => 'IT']);
        $childActivity = Activity::factory()->create([
            'name' => 'Разработка',
            'parent_id' => $rootActivity->id
        ]);
        $grandchildActivity = Activity::factory()->create([
            'name' => 'Веб-разработка',
            'parent_id' => $childActivity->id
        ]);

        // Создаем организации
        $org1 = Organization::factory()->create(['name' => 'WebDev Inc']);
        $org2 = Organization::factory()->create(['name' => 'DevExperts']);

        $org1->activities()->attach($grandchildActivity);
        $org2->activities()->attach($childActivity);

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/organizations/search/activity/{$rootActivity->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'WebDev Inc'])
            ->assertJsonFragment(['name' => 'DevExperts']);
    }

    #[Test]
    public function it_returns_empty_list_when_no_organizations_in_tree()
    {
        $activity = Activity::factory()->create();

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson("/api/organizations/search/activity/{$activity->id}");

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
        ])->getJson("/api/organizations/search/activity/{$nonExistentId}");

        $response->assertStatus(404);
    }
}
