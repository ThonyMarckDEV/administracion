<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear la tabla series_correlatives
        Schema::create('series_correlatives', function (Blueprint $table) {
            $table->id();
            $table->enum('document_type', ['B', 'F', 'RHE', 'RA'])->comment('B: Boleta, F: Factura, RHE: Recibo por Honorarios, RA: Dada de baja');
            $table->string('serie', 10);
            $table->unsignedBigInteger('correlative');
            $table->timestamps();
            $table->unique(['document_type', 'serie']);
        });

        // Insertar registros por defecto
        $defaultRecords = [
            ['document_type' => 'B', 'serie' => '001', 'correlative' => 0],
            ['document_type' => 'F', 'serie' => '001', 'correlative' => 0],
            ['document_type' => 'RHE', 'serie' => '001', 'correlative' => 0],
            ['document_type' => 'RA', 'serie' => '001', 'correlative' => 0],
        ];

        foreach ($defaultRecords as $record) {
            DB::table('series_correlatives')->insert([
                'document_type' => $record['document_type'],
                'serie' => $record['serie'],
                'correlative' => $record['correlative'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series_correlatives');
    }
};