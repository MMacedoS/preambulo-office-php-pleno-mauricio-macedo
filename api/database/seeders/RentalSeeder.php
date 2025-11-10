<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental\Locacao;
use App\Models\Rental\LocacaoFilme;
use App\Models\User;
use App\Models\Movies\Filme;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = User::where('role', 'cliente')->get();
        $filmes = Filme::all();

        if ($clientes->count() < 1 || $filmes->count() < 1) {
            return;
        }

        $filmeArray = $filmes->toArray();
        $filmesIds = array_column($filmeArray, 'id');

        foreach ($clientes as $index => $cliente) {
            $numLocacoes = rand(1, 4);

            for ($i = 0; $i < $numLocacoes; $i++) {
                $statusOptions = ['ativo', 'ativo', 'ativo', 'devolvido', 'atrasado'];
                $status = $statusOptions[array_rand($statusOptions)];

                $dataInicio = now()->subDays(rand(1, 60));
                $dataDevolucao = $dataInicio->copy()->addDays(rand(2, 7));

                $multa = $status === 'atrasado' ? rand(10, 50) : 0;

                $locacao = Locacao::create([
                    'usuario_id' => $cliente->id,
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'data_inicio' => $dataInicio,
                    'data_devolucao' => $dataDevolucao,
                    'valor_total' => 0,
                    'status' => $status,
                    'multa' => $multa,
                ]);

                $numFilmes = rand(1, 3);
                $filmeSelecionados = array_rand($filmesIds, min($numFilmes, count($filmesIds)));

                if (!is_array($filmeSelecionados)) {
                    $filmeSelecionados = [$filmeSelecionados];
                }

                $valorTotal = 0;

                foreach ($filmeSelecionados as $filmeIndex) {
                    $filmeId = $filmesIds[$filmeIndex];
                    $filme = Filme::find($filmeId);

                    if ($filme) {
                        $valorUnitario = $filme->valor_aluguel;
                        $valorTotal += $valorUnitario;

                        LocacaoFilme::create([
                            'locacao_id' => $locacao->id,
                            'filme_id' => $filmeId,
                            'quantidade' => 1,
                            'preco_unitario' => $valorUnitario,
                        ]);
                    }
                }

                $locacao->update(['valor_total' => $valorTotal]);
            }
        }
    }
}
