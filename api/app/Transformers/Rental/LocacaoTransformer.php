<?php

namespace App\Transformers\Rental;

use App\Models\Rental\Locacao;
use App\Repositories\Entities\Users\UsuarioRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Traits\TransformerTrait;
use App\Transformers\Movies\FilmeTransformer;

class LocacaoTransformer
{
    use TransformerTrait;

    public function transform(Locacao $locacao): array
    {
        return [
            'id' => $locacao->uuid,
            'code' => $locacao->id,
            'client' => $this->prepareCliente($locacao->usuario_id),
            'rental_date' => $locacao->data_inicio,
            'return_date' => $locacao->data_devolucao,
            'total_value' => $locacao->valor_total,
            'penalty' => $locacao->multa,
            'movies' => $this->prepareMovies($locacao->filmes),
            'status' => $locacao->status,
            'created_at' => $locacao->created_at,
            'updated_at' => $locacao->updated_at,
        ];
    }

    public function prepareCliente(int $usuarioId): ?array
    {
        $usuario = UsuarioRepository::getInstance()->findById($usuarioId);
        if (!$usuario) {
            return null;
        }

        return [
            'id' => $usuario->uuid,
            'name' => $usuario->name,
            'email' => $usuario->email,
        ];
    }

    public function prepareMovies($filmes): array
    {
        $filmeTransformer = new FilmeTransformer();
        return $filmeTransformer->transformCollection($filmes);
    }

    public function transformCollection(Collection $locacoes)
    {
        if ($locacoes->isEmpty()) {
            return [];
        }
        return array_values($locacoes->map([$this, 'transform'])->toArray());
    }

    public function originalAttribute()
    {
        return [
            'id' => 'uuid',
            'code' => 'id',
            'client' => 'usuario_id',
            'rental_date' => 'data_inicio',
            'return_date' => 'data_devolucao',
            'total_value' => 'valor_total',
            'penalty' => 'multa',
            'status' => 'status',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
    }

    public function transformArray(array $data)
    {
        return $this->transformKeys($data, $this->originalAttribute());
    }
}
