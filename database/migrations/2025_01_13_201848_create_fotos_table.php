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
        Schema::create('fotos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('casa_id');
            $table->text('base64');
            $table->string('path_webp'); //formato webp
            $table->text('foto_base64')->nullable(); // Foto en formato Base64
            $table->string('foto_url')->nullable(); // URL de la foto (en almacenamiento externo)
            $table->binary('foto_binaria')->nullable();
            
            $table->foreign('casa_id')
                ->references('id')
                ->on('casas')
                ->onDelete('restrict'); //cascade | set null 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fotos');
    }
};
