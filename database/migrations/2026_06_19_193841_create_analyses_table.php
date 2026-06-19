<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidats')->cascadeOnDelete();
            $table->json('extracted_skills')->nullable();
            $table->unsignedInteger('years_experience')->default(0);
            $table->string('education_level')->nullable();
            $table->json('languages')->nullable();
            $table->unsignedInteger('matching_score')->default(0);
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('missing_skills')->nullable();
            $table->string('recommendation')->nullable();
            $table->text('justification')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
