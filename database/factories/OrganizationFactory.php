<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'building_id' => Building::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withActivities($count = 1): Factory
    {
        return $this->afterCreating(function (Organization $organization) use ($count) {
            $organization->activities()->attach(
                \App\Models\Activity::factory()->count($count)->create()
            );
        });
    }

    public function withPhones($count = 1): Factory
    {
        return $this->afterCreating(function (Organization $organization) use ($count) {
            \App\Models\OrganizationPhone::factory()
                ->count($count)
                ->for($organization)
                ->create();
        });
    }
}
