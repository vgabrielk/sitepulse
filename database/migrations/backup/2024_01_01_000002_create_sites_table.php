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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('domain');
            $table->string('widget_id')->unique();
            $table->json('widget_config')->nullable(); // Widget customization settings
            $table->json('tracking_config')->nullable(); // Tracking settings
            $table->boolean('is_active')->default(true);
            $table->boolean('anonymize_ips')->default(true);
            $table->boolean('track_events')->default(true);
            $table->boolean('collect_feedback')->default(true);
            $table->timestamps();
            
            $table->index(['client_id', 'is_active']);
            $table->index('widget_id');
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
