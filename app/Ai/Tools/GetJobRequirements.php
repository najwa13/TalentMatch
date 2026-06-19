<?php

namespace App\Ai\Tools;

use App\Models\Offer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetJobRequirements implements Tool
{
    public function description(): Stringable|string
    {
        return 'Récupérer les prérequis d\'une offre d\'emploi par son ID. Retourne le titre, la description, les compétences requises et l\'expérience minimale.';
    }

    public function handle(Request $request): Stringable|string
    {
        $offerId = $request->input('offer_id');

        $offer = Offer::find($offerId);

        if (! $offer) {
            return json_encode(['error' => 'Offre introuvable.']);
        }

        return json_encode([
            'title' => $offer->title,
            'description' => $offer->description,
            'required_skills' => $offer->required_skills,
            'minimum_experience' => $offer->minimum_experience,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'offer_id' => $schema->integer()->description('L\'ID de l\'offre d\'emploi')->required(),
        ];
    }
}
