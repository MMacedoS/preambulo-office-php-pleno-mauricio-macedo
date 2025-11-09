<?php

namespace App\Transformers\Movies;

use App\Models\Movies\Filme;
use App\Traits\TransformerTrait;
use Illuminate\Database\Eloquent\Collection;

class FilmeTransformer
{
    use TransformerTrait;
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

    public function transformCollection(Collection $filmes)
    {
        if ($filmes->isEmpty()) {
            return [];
        }
        return array_values($filmes->map([$this, 'transform'])->toArray());
    }

    public function originalAttribute()
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

    public function transformArray(array $data)
    {
        return $this->transformKeys($data, $this->originalAttribute());
    }
}
