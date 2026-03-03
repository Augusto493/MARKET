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
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->unique()->after('id');
        });

        // Preencher codigo para reservas existentes
        $reservations = \DB::table('reservations')->get();
        foreach ($reservations as $r) {
            $codigo = 'RES-' . strtoupper(substr(md5(uniqid((string) $r->id, true)), 0, 8));
            \DB::table('reservations')->where('id', $r->id)->update(['codigo' => $codigo]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};
