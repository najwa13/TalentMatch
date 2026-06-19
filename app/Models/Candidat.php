<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cv_text',
    ];

    public function analyses(): HasMany
    {
        return $this->hasMany(Analyse::class, 'candidate_id');
    }
}
