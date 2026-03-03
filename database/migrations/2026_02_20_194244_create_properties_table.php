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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            
            // IDs da Stays
            $table->string('stays_property_id')->unique();
            $table->string('stays_unit_id')->nullable();
            
            // Dados básicos
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->text('descricao_curta')->nullable();
            
            // Capacidade
            $table->integer('capacidade_hospedes')->default(1);
            $table->integer('quartos')->default(0);
            $table->integer('camas')->default(0);
            $table->integer('banheiros')->default(0);
            
            // Localização (sem endereço exato)
            $table->string('cidade')->default('Balneário Camboriú');
            $table->string('bairro')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Status e visibilidade
            $table->boolean('ativo')->default(true);
            $table->boolean('publicado_marketplace')->default(false);
            $table->integer('prioridade')->default(0);
            $table->boolean('destaque')->default(false);
            
            // Conteúdo local (override)
            $table->string('titulo_marketing')->nullable();
            $table->json('tags')->nullable();
            
            // Dados da Stays (raw JSON para referência)
            $table->json('stays_raw_data')->nullable();
            
            // Timestamps
            $table->timestamp('stays_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('owner_id');
            $table->index('ativo');
            $table->index('publicado_marketplace');
            $table->index('cidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
