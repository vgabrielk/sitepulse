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
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // visits, sessions, events, etc.
            $table->string('metric_name'); // specific metric name
            $table->decimal('value', 15, 4);
            $table->date('date');
            $table->string('period')->default('daily'); // daily, weekly, monthly
            $table->json('dimensions')->nullable(); // Additional metric dimensions
            $table->timestamps();
            
            $table->index(['site_id', 'metric_type', 'date']);
            $table->index(['metric_type', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
