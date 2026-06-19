<?php

namespace App\Http\Controllers;

use App\Enums\AnalysisStatus;
use App\Http\Requests\AnalyzeCvRequest;
use App\Jobs\AnalyzeCvJob;
use App\Models\Analyse;
use App\Models\Candidat;
use App\Models\Offer;

class CandidateAnalysisController extends Controller
{
    public function store(AnalyzeCvRequest $request, Offer $offre)
    {
        $candidat = Candidat::create([
            'name' => $request->input('candidate_name'),
            'cv_text' => $request->input('cv_text'),
        ]);

        $analyse = Analyse::create([
            'job_offer_id' => $offre->id,
            'candidate_id' => $candidat->id,
            'status' => AnalysisStatus::Pending,
        ]);

        AnalyzeCvJob::dispatch($analyse);

        return response()->json([
            'message' => 'Analyse en cours...',
            'analysis_id' => $analyse->id,
        ], 202);
    }

    public function show(Analyse $analyse)
    {
        $analyse->load('candidat', 'offer');

        return response()->json($analyse);
    }
}
