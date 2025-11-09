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
        Schema::create('locacao_filme', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('locacao_id');
            $table->unsignedInteger('filme_id');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 8, 2);
            $table->foreign('locacao_id')->references('id')->on('locacoes')->onDelete('cascade');
            $table->foreign('filme_id')->references('id')->on('filmes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locacao_filme');
    }
};
