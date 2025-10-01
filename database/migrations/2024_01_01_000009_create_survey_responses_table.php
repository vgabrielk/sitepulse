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
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('analytics_sessions')->onDelete('set null');
            $table->json('responses'); // Survey responses data
            $table->string('ip_address')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
            
            $table->index(['survey_id', 'submitted_at']);
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
