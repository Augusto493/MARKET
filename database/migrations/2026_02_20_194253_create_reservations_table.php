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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            
            // Dados do hóspede
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
            $table->integer('guests_count')->default(1);
            
            // Datas
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->integer('nights');
            
            // Valores
            $table->decimal('base_total', 10, 2);
            $table->decimal('markup_total', 10, 2)->default(0);
            $table->decimal('final_total', 10, 2);
            $table->decimal('cleaning_fee', 10, 2)->nullable();
            $table->string('currency', 3)->default('BRL');
            
            // Status
            $table->enum('status', ['lead', 'pending', 'confirmed', 'cancelled', 'failed'])->default('lead');
            
            // Integração Stays
            $table->string('stays_reservation_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('raw_payload_json')->nullable();
            
            // Origem
            $table->string('origem')->default('marketplace'); // marketplace, admin, api
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['owner_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['checkin_date', 'checkout_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
