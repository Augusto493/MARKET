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
        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('cascade');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('cascade');
            
            $table->string('tipo'); // api_call, webhook, reservation_create, etc
            $table->enum('status', ['success', 'error', 'warning'])->default('success');
            $table->string('endpoint')->nullable();
            $table->string('method')->nullable(); // GET, POST, etc
            $table->integer('status_code')->nullable();
            $table->text('request_body')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->decimal('duration_ms', 10, 2)->nullable();
            
            $table->timestamps();
            
            $table->index(['owner_id', 'tipo', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
    }
};
