<?php

namespace Database\Factories;

use App\Models\Analyse;
use App\Models\Candidat;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Analyse>
 */
class AnalyseFactory extends Factory
{
    protected $model = Analyse::class;

    public function definition(): array
    {
        return [
            'job_offer_id' => Offer::factory(),
            'candidate_id' => Candidat::factory(),
            'extracted_skills' => fake()->randomElements(['PHP', 'Laravel', 'JavaScript', 'React'], 2),
            'years_experience' => fake()->numberBetween(0, 15),
            'education_level' => fake()->randomElement(['Licence', 'Master', 'Doctorat', 'BTS']),
            'languages' => fake()->randomElements(['Français', 'Anglais', 'Arabe', 'Espagnol'], 2),
            'matching_score' => fake()->numberBetween(0, 100),
            'strengths' => fake()->randomElements(['Expérience', 'Compétences techniques', 'Soft skills'], 2),
            'weaknesses' => fake()->randomElements(['Manque d\'expérience', 'Compétences manquantes'], 1),
            'missing_skills' => fake()->randomElements(['Docker', 'Kubernetes', 'AWS'], 1),
            'recommendation' => fake()->randomElement(['convoquer', 'attente', 'rejeter']),
            'justification' => fake()->paragraph(2),
            'status' => fake()->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}
