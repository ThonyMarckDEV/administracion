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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->enum('document_type', ['B', 'F'])->comment('B: Boleta, F: Factura');
            $table->string('serie_assigned', 10);
            $table->string('correlative_assigned', 20);
            $table->enum('sunat', ['anulado', 'enviado'])->nullable()->comment('Estado del comprobante ante SUNAT');
            $table->timestamps();
            $table->unique(['serie_assigned', 'correlative_assigned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};