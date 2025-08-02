<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'parent_id' => null,
        ];
    }

    public function withChildren(): Factory
    {
        return $this->afterCreating(function (Activity $activity) {
            // Создаем 1-2 потомков первого уровня
            $firstLevelCount = $this->faker->numberBetween(1, 2);

            for ($i = 0; $i < $firstLevelCount; $i++) {
                $firstLevelChild = Activity::factory()
                    ->create(['parent_id' => $activity->id]);

                // Создаем 0-5 потомков второго уровня (80% вероятность)
                if ($this->faker->boolean(0.8)) {
                    $secondLevelCount = $this->faker->numberBetween(1, 3);

                    for ($j = 0; $j < $secondLevelCount; $j++) {
                        Activity::factory()
                            ->create(['parent_id' => $firstLevelChild->id]);
                    }
                }
            }
        });
    }
}
