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
        Schema::create('property_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('tipo'); // checkin_time, checkout_time, min_nights, max_nights, pet_friendly, smoking, etc
            $table->text('valor')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();
            
            $table->index(['property_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_rules');
    }
};
