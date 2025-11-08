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
        Schema::create('filmes', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->string('titulo');
            $table->text('sinopse')->nullable();
            $table->string('diretor')->nullable();
            $table->string('categoria')->nullable();
            $table->year('ano_lancamento')->nullable();
            $table->float('valor_aluguel', 8, 2);
            $table->integer('quantidade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filmes');
    }
};
