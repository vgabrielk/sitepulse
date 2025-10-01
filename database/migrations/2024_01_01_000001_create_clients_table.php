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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('api_key')->unique();
            $table->string('webhook_url')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->enum('plan', ['free', 'basic', 'premium', 'enterprise'])->default('free');
            $table->json('plan_limits')->nullable(); // Store plan limits as JSON
            $table->json('settings')->nullable(); // Store client-specific settings
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamps();
            
            $table->index(['api_key', 'is_active']);
            $table->index('plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
