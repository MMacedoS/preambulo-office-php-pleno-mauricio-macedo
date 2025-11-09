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
        Schema::create('locacoes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->uuid('uuid')->unique();
            $table->date('data_inicio');
            $table->date('data_devolucao');
            $table->decimal('valor_total', 8, 2);
            $table->enum('status', ['ativo', 'devolvido', 'atrasado'])->default('ativo');
            $table->decimal('multa', 8, 2)->default(0);
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locacao');
    }
};
