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
        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('url');
            $table->string('thumbnail_url')->nullable();
            $table->string('stays_photo_id')->nullable();
            $table->string('hash')->nullable(); // Para reconciliar
            $table->integer('ordem')->default(0);
            $table->boolean('principal')->default(false);
            $table->text('legenda')->nullable();
            $table->timestamps();
            
            $table->index('property_id');
            $table->index('principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_photos');
    }
};
