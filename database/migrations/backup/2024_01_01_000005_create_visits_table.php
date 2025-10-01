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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('analytics_sessions')->onDelete('cascade');
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('title')->nullable();
            $table->timestamp('visited_at');
            $table->integer('time_on_page')->nullable(); // in seconds
            $table->integer('scroll_depth')->nullable(); // percentage
            $table->boolean('is_bounce')->default(false);
            $table->boolean('is_exit')->default(false);
            $table->json('page_data')->nullable(); // Additional page metadata
            $table->timestamps();
            
            $table->index(['session_id', 'visited_at']);
            $table->index(['page_id', 'visited_at']);
            $table->index('visited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
