<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MyCompany;

class CreateMyCompanyTable extends Migration
{
    public function up()
    {
        Schema::create('mycompany', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11)->unique();
            $table->string('razon_social', 255);
            $table->string('nombre_comercial', 255);
            $table->string('ubigueo', 6);
            $table->string('departamento', 100);
            $table->string('provincia', 100);
            $table->string('distrito', 100);
            $table->string('urbanizacion', 100)->nullable();
            $table->string('direccion', 255);
            $table->string('cod_local', 4);
            $table->timestamps();
        });

        // Insert default data
        MyCompany::create([
            'ruc' => '12345678901',
            'razon_social' => 'EMPRESA TEST',
            'nombre_comercial' => 'TEST SAC',
            'ubigueo' => '150101',
            'departamento' => 'LIMA',
            'provincia' => 'LIMA',
            'distrito' => 'LIMA',
            'urbanizacion' => 'URB TEST',
            'direccion' => 'AV TEST 123',
            'cod_local' => '0000',
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('mycompany');
    }
}