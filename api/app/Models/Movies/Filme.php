<?php

namespace App\Models\Movies;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filme extends Model
{
    use HasUuid, HasFactory;

    protected $table = 'filmes';

    protected $fillable = [
        'uuid',
        'titulo',
        'sinopse',
        'diretor',
        'categoria',
        'ano_lancamento',
        'valor_aluguel',
        'quantidade',
    ];

    public function locacaoFilmes()
    {
        return $this->hasMany(\App\Models\Rental\LocacaoFilme::class, 'filme_id');
    }
}
