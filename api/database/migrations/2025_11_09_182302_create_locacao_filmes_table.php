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
        Schema::create('locacao_filmes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locacao_id');
            $table->unsignedBigInteger('filme_id');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('locacao_id')->references('id')->on('locacoes')->onDelete('cascade');
            $table->foreign('filme_id')->references('id')->on('filmes')->onDelete('cascade');

            $table->unique(['locacao_id', 'filme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locacao_filmes');
    }
};
