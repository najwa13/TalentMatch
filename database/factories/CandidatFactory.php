<?php

namespace Database\Factories;

use App\Models\Candidat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidat>
 */
class CandidatFactory extends Factory
{
    protected $model = Candidat::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'cv_text' => fake()->paragraphs(3, true),
        ];
    }
}
