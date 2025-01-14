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
        Schema::create('casas', function (Blueprint $table) {
            
            $table->id();
            $table->SoftDeletes();
            $table->string('nombre')->nullable();
            $table->string('tipo_inmueble')->default('Apartamento');
            $table->string('barrio');
            $table->bigInteger('precio');
            
            
            $table->string('ventaOarriendo')->nullable();
            $table->string('usado')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('pais')->default('Colombia');
            $table->string('inmoviliaria')->nullable();
            $table->string('tamano_m2')->nullable();
            $table->string('contacto_celular')->nullable();
            $table->string('contacto_celular2')->nullable();
            $table->integer('estrato')->nullable();
            $table->string('estado')->nullable();
            $table->integer('antiguedad')->nullable();
            $table->integer('parqueaderos')->nullable();
            $table->integer('banos')->nullable();
            $table->integer('habitaciones')->nullable();
            $table->boolean('acepta_mascotas')->default(false);
            $table->smallInteger('administracion')->nullable();
            $table->string('contrato_minimo')->nullable();
            $table->string('cambio_precio1')->nullable();
            $table->string('cambio_precio2')->nullable();
            $table->string('pisos_interiores')->nullable();
            $table->string('terraza')->nullable();
            $table->text('comodidades')->nullable();
            $table->date('fecha_contacto1')->nullable();
            $table->text('conclusion_contacto1')->nullable();
            $table->timestamp('registro_fecha_contacto1')->nullable();
            
            $table->date('fecha_contacto2')->nullable();
            $table->text('conclusion_contacto2')->nullable();
            $table->timestamp('registro_fecha_contacto2')->nullable();
            
            $table->date('fecha_primera_aceptacion_cliente')->nullable();
            $table->text('fecha_conversacion_arrendatario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casas');
    }
};
