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
        Schema::create('property_calendar_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->date('data');
            $table->enum('status', ['available', 'booked', 'blocked', 'unavailable'])->default('available');
            $table->integer('min_nights')->nullable();
            $table->integer('max_nights')->nullable();
            $table->timestamp('cached_at');
            $table->timestamps();
            
            $table->unique(['property_id', 'data']);
            $table->index(['property_id', 'data', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_calendar_cache');
    }
};
