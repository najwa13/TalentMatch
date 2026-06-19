<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->foreignId('analysis_id')->nullable()->constrained('analyses')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('analysis_id');
        });
    }
};
