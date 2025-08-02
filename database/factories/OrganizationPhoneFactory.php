<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationPhone>
 */
class OrganizationPhoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => $this->generatePhoneNumber(),
        ];
    }

    private function generatePhoneNumber(): string
    {
        $formats = [
            '###-###-####',
            '(###) ###-####',
            '### ### ####',
            '##########',
            '+7 ### ### ####',
        ];

        return $this->faker->numerify($this->faker->randomElement($formats));
    }
}
