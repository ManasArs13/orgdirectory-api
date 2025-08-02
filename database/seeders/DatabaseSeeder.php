<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Конфигурация количества записей
    public const BUILDING_COUNT = 10;
    public const ACTIVITY_MAIN_COUNT = 30;
    public const ORGANIZATION_PHONE_COUNT = 3;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Activity::factory(self::ACTIVITY_MAIN_COUNT)->withChildren()->create();

        Building::factory(self::BUILDING_COUNT)
            ->has(
                Organization::factory()
                    ->count(rand(1, 5))
                    ->hasAttached(
                        Activity::inRandomOrder()->limit(rand(1, 4))->get()
                    )
                    ->withActivities(1)
                    ->withPhones(rand(1, 3)),
                'organizations'
            )
            ->create();

        $this->printStatistics();
    }

    /**
     * Вывод статистики по заполненным данным
     */
    protected function printStatistics()
    {
        $this->command->table(
            ['Таблица', 'Всего записей'],
            [
                ['Деятельность', Activity::count()],
                ['Здания', Building::count()],
                ['Организации', Organization::count()],
            ]
        );
    }
}
