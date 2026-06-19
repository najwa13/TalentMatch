<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(3),
            'required_skills' => [fake()->randomElement(['PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'MySQL', 'PostgreSQL', 'Docker', 'Git', 'API REST'])],
            'minimum_experience' => fake()->numberBetween(0, 10),
            'user_id' => User::factory(),
        ];
    }

    public function withMultipleSkills(): static
    {
        return $this->state(fn (array $attributes) => [
            'required_skills' => fake()->randomElements(['PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'MySQL', 'PostgreSQL', 'Docker', 'Git', 'API REST', 'Tailwind CSS', 'Alpine.js'], 3),
        ]);
    }
}
