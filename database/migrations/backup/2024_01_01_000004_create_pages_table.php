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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('title')->nullable();
            $table->string('path');
            $table->string('query_string')->nullable();
            $table->string('hash')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('unique_views_count')->default(0);
            $table->decimal('avg_time_on_page', 8, 2)->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->timestamps();
            
            $table->index(['site_id', 'url']);
            $table->index(['site_id', 'path']);
            $table->index('views_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
