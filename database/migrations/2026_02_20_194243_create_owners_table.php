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
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // Credenciais Stays.net
            $table->string('stays_base_url')->nullable();
            $table->string('stays_client_id')->nullable();
            $table->text('stays_client_secret')->nullable(); // encrypted
            $table->text('stays_token')->nullable(); // encrypted
            $table->string('stays_account_identifier')->nullable();
            $table->string('webhook_secret')->nullable();
            
            // Status de sincronização
            $table->enum('sync_status', ['ok', 'erro', 'pending'])->default('pending');
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_sync_error')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
