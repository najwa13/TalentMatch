<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\CandidateAnalysisController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/offers/{offre}/analyses', [CandidateAnalysisController::class, 'store'])
        ->name('api.analyses.store');

    Route::get('/analyses/{analyse}', [CandidateAnalysisController::class, 'show'])
        ->name('api.analyses.show');

    Route::post('/assistant/ask', [AssistantController::class, 'ask'])
        ->name('api.assistant.ask');
});
