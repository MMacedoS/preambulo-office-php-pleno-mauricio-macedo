<?php

namespace App\Models\films;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Filme extends Model
{
    use HasUuid;

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
}
