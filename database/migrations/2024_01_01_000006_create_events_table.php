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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // click, scroll, form_submit, etc.
            $table->string('element_selector')->nullable(); // CSS selector
            $table->string('element_text')->nullable(); // Text content of element
            $table->string('element_tag')->nullable(); // HTML tag name
            $table->json('coordinates')->nullable(); // x, y coordinates
            $table->json('event_data')->nullable(); // Additional event metadata
            $table->timestamp('occurred_at');
            $table->timestamps();
            
            $table->index(['visit_id', 'event_type']);
            $table->index(['event_type', 'occurred_at']);
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
