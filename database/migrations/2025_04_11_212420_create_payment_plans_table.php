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
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('period_id')->constrained('periods');
            $table->boolean('payment_type')->default(true)->comment('true: anual, false: mensual');
            $table->decimal('amount', 6,2)->default(0);
            $table->unsignedInteger('duration')->default(0);
            $table->boolean('state')->default(true)->comment('true: activo, false: inactivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
