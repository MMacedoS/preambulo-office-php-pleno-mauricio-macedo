<?php

namespace App\Models\Rental;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasUuid, HasFactory;

    protected $table = 'locacoes';

    protected $fillable = [
        'usuario_id',
        'uuid',
        'data_inicio',
        'data_devolucao',
        'valor_total',
        'status',
        'multa',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function locacaoFilmes()
    {
        return $this->hasMany(LocacaoFilme::class, 'locacao_id');
    }

    public function filmes()
    {
        return $this->belongsToMany(
            \App\Models\Movies\Filme::class,
            'locacao_filme',
            'locacao_id',
            'filme_id'
        )->withPivot('quantidade', 'preco_unitario');
    }
}
