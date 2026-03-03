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
        Schema::create('markup_rules', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('tipo', ['global', 'owner', 'property'])->default('global');
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('cascade');
            
            // Tipo de markup
            $table->enum('markup_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('markup_value', 10, 2);
            
            // Regras avançadas (opcional)
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->json('dias_semana')->nullable(); // [1,2,3] = segunda, terça, quarta
            $table->integer('min_noites')->nullable();
            $table->integer('max_noites')->nullable();
            
            $table->boolean('ativo')->default(true);
            $table->integer('prioridade')->default(0); // maior prioridade = aplica primeiro
            $table->timestamps();
            
            $table->index(['tipo', 'ativo']);
            $table->index('owner_id');
            $table->index('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markup_rules');
    }
};
