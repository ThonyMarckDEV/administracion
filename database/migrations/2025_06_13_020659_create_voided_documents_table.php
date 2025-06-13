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
        Schema::create('voided_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade')->comment('ID del comprobante asociado');
            $table->string('correlativo_baja', 20)->unique()->comment('Correlativo del comunicado de baja (ej. RA-YYYYMMDD-NNN)');
            $table->date('fec_generacion')->comment('Fecha de generación del comprobante');
            $table->date('fec_comunicacion')->comment('Fecha de comunicación a SUNAT');
            $table->string('motivo')->comment('Motivo de la anulación');
            $table->string('xml_path')->nullable()->comment('Ruta del archivo XML generado');
            $table->string('cdr_path')->nullable()->comment('Ruta del archivo CDR (respuesta de SUNAT)');
            $table->string('ticket')->nullable()->comment('Ticket de SUNAT para el comunicado de baja');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->comment('Estado del comunicado ante SUNAT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voided_documents');
    }
};