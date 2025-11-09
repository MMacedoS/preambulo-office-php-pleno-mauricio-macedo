<?php

namespace App\Models\Rental;

use App\Models\Movies\Filme;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocacaoFilme extends Model
{
    use HasFactory;

    protected $table = 'locacao_filme';

    protected $fillable = [
        'locacao_id',
        'filme_id',
        'quantidade',
        'preco_unitario',
    ];

    public function locacao()
    {
        return $this->belongsTo(Locacao::class, 'locacao_id');
    }

    public function filme()
    {
        return $this->belongsTo(Filme::class, 'filme_id');
    }
}
