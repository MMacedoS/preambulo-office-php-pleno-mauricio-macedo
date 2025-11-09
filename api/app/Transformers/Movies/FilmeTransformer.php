<?php

namespace App\Transformers\Movies;

use App\Models\Movies\Filme;

class FilmeTransformer
{
    public function transform(Filme $filme)
    {
        return [
            'id' => $filme->uuid,
            'code' => $filme->id,
            'title' => $filme->titulo,
            'director' => $filme->diretor,
            'description' => $filme->sinopse,
            'release_year' => $filme->ano_lancamento,
            'genre' => $filme->categoria,
            'quantity' => $filme->quantidade,
            'rental_price' => $filme->valor_aluguel,
        ];
    }

    public function transformCollection($filmes)
    {
        return array_map([$this, 'transform'], $filmes->toArray());
    }

    public function originalAttribute($index)
    {
        return [
            'id' => 'uuid',
            'code' => 'id',
            'title' => 'titulo',
            'director' => 'diretor',
            'description' => 'sinopse',
            'release_year' => 'ano_lancamento',
            'genre' => 'categoria',
            'quantity' => 'quantidade',
            'rental_price' => 'valor_aluguel',
        ];
    }
}
