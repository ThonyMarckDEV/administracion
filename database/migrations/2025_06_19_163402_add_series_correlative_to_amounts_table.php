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
        Schema::table('category_supplier', function (Blueprint $table) {
            $table->string('serie_assigned', 10)->nullable()->after('date_init')->comment('Serie asignada para el Recibo por Honorarios');
            $table->unsignedBigInteger('correlative_assigned')->nullable()->after('serie_assigned')->comment('Correlativo asignado para el Recibo por Honorarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_supplier', function (Blueprint $table) {
            $table->dropColumn(['serie_assigned', 'correlative_assigned']);
        });
    }
};
