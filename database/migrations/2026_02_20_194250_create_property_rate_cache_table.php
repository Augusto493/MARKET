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
        Schema::create('property_rate_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->date('data');
            $table->decimal('preco_base', 10, 2);
            $table->string('moeda', 3)->default('BRL');
            $table->decimal('taxa_limpeza', 10, 2)->nullable();
            $table->timestamp('cached_at');
            $table->timestamps();
            
            $table->unique(['property_id', 'data']);
            $table->index(['property_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_rate_cache');
    }
};
