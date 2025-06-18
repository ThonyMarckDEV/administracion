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
        Schema::create('series_correlatives', function (Blueprint $table) {
            $table->id();
            $table->enum('document_type', ['B', 'F' , 'RA'])->comment('B: Boleta, F: Factura, RA: Dada de baja');
            $table->string('serie', 10);
            $table->unsignedBigInteger('correlative');
            $table->timestamps();
            $table->unique(['document_type', 'serie']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series_correlatives');
    }
};