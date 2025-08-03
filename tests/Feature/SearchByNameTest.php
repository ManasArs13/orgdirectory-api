<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\CheckStaticKey;
use App\Http\Middleware\VerifyApiKey;
use App\Models\Organization;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchByNameTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_finds_organizations_by_name_part()
    {
        Organization::factory()->create(['name' => 'ООО Ромашка']);
        Organization::factory()->create(['name' => 'АО СтройТех']);
        Organization::factory()->create(['name' => 'ИП Иванов']);

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson('/api/organizations/search/name/Рома');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'ООО Ромашка'])
            ->assertJsonMissing(['name' => 'АО СтройТех'])
            ->assertJsonMissing(['name' => 'ИП Иванов']);
    }

    #[Test]
    public function it_returns_empty_list_when_no_matches()
    {
        Organization::factory()->create(['name' => 'ООО Ромашка']);

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson('/api/organizations/search/name/несуществующее');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_returns_multiple_matches()
    {
        Organization::factory()->create(['name' => 'ООО Технологии']);
        Organization::factory()->create(['name' => 'АО ТехноПром']);
        Organization::factory()->create(['name' => 'ИП Иванов']);

        $response = $this->withoutMiddleware([
            CheckStaticKey::class,
            VerifyApiKey::class
        ])->getJson('/api/organizations/search/name/Тех');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
