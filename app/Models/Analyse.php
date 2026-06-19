<?php

namespace App\Models;

use App\Enums\AnalysisStatus;
use App\Enums\Recommendation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Analyse extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_offer_id',
        'candidate_id',
        'extracted_skills',
        'years_experience',
        'education_level',
        'languages',
        'matching_score',
        'strengths',
        'weaknesses',
        'missing_skills',
        'recommendation',
        'justification',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'extracted_skills' => 'array',
            'years_experience' => 'integer',
            'languages' => 'array',
            'matching_score' => 'integer',
            'strengths' => 'array',
            'weaknesses' => 'array',
            'missing_skills' => 'array',
            'recommendation' => Recommendation::class,
            'status' => AnalysisStatus::class,
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'job_offer_id');
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class, 'candidate_id');
    }
}
