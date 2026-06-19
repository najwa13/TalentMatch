<?php

namespace App\Ai\Agents;

use App\Models\Offer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class CvAnalysisAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        public Offer $offer,
        public string $candidateName,
        public string $cvText,
    ) {}

    public function instructions(): Stringable|string
    {
        return sprintf(
            "Tu es un assistant RH spécialisé dans l'analyse de CV. Ton rôle est d'analyser un CV par rapport à une offre d'emploi et de produire un résultat JSON structuré.

Offre d'emploi :
- Titre : %s
- Compétences requises : %s
- Expérience minimale requise : %d ans

CV du candidat (%s) :
%s

Règles :
1. Extrais les compétences techniques et non-techniques du CV
2. Estime les années d'expérience totales du candidat
3. Identifie le niveau d'études le plus élevé
4. Liste les langues parlées avec leur niveau
5. Calcule un score de matching (0-100) entre le CV et l'offre
6. Identifie les points forts et les lacunes
7. Détermine les compétences manquantes par rapport à l'offre
8. Produis une recommandation : convoquer (score >= 70), attente (40-69), rejeter (< 40)
9. Justifie la recommandation en 2-3 phrases

Réponds UNIQUEMENT avec le JSON structuré, sans texte additionnel.",
            $this->offer->title,
            implode(', ', $this->offer->required_skills ?? []),
            $this->offer->minimum_experience ?? 0,
            $this->candidateName,
            $this->cvText,
        );
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'competences_extraites' => $schema->array(
                $schema->string()
            )->required(),
            'annees_experience' => $schema->integer()->min(0)->required(),
            'niveau_etudes' => $schema->string()->required(),
            'langues' => $schema->array(
                $schema->string()
            )->required(),
            'matching_score' => $schema->integer()->min(0)->max(100)->required(),
            'points_forts' => $schema->array(
                $schema->string()
            )->required(),
            'lacunes' => $schema->array(
                $schema->string()
            )->required(),
            'competences_manquantes' => $schema->array(
                $schema->string()
            )->required(),
            'recommandation' => $schema->string()->enum(['convoquer', 'attente', 'rejeter'])->required(),
            'justification' => $schema->string()->required(),
        ];
    }
}
