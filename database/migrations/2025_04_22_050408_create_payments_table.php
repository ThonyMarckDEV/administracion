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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('payment_plan_id')->constrained('payment_plans');
            $table->foreignId('discount_id')->nullable()->constrained('discounts');
            $table->decimal('amount', 8, 2)->default(0);
            $table->timestamp('payment_date')->nullable();
            $table->enum('payment_method', ['efectivo', 'transferencia'])->default('efectivo');
            $table->string('reference')->nullable();
            $table->enum('status', ['pendiente', 'pagado', 'vencido'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
