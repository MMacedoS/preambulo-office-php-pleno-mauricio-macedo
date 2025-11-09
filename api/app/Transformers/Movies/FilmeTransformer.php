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
            'created_at' => $filme->created_at,
            'updated_at' => $filme->updated_at,
        ];
    }

    public function transformCollection($filmes)
    {
        return array_map([$this, 'transform'], $filmes->toArray());
    }
}
