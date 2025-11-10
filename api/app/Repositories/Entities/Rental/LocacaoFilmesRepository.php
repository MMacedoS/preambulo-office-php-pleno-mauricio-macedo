<?php

namespace App\Repositories\Entities\Rental;

use App\Models\Rental\Locacao;
use App\Models\Rental\LocacaoFilme;
use App\Repositories\Contracts\Rental\ILocacaoFilmesRepository;
use App\Repositories\Entities\Movies\FilmeRepository;
use App\Repositories\Traits\CacheTrait;
use App\Repositories\Traits\SingletonTrait;

class LocacaoFilmesRepository implements ILocacaoFilmesRepository
{
    use SingletonTrait, CacheTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new LocacaoFilme();
    }

    public function validateMovieStock(array $movieIds): ?array
    {
        $filmeRepository = FilmeRepository::getInstance();
        $filmeIds = [];

        foreach ($movieIds as $uuid) {
            $filme = $filmeRepository->findByUuid($uuid);
            if ($filme) {
                $filmeIds[] = $filme->id;
            }
        }

        if (empty($filmeIds)) {
            return $movieIds;
        }

        $moviesWithInsufficientStock = $filmeRepository->findByIdsWithInsufficientStock($filmeIds);
        return empty($moviesWithInsufficientStock) ? null : $moviesWithInsufficientStock;
    }

    public function getAvailableMovies(array $movieIds)
    {
        return FilmeRepository::getInstance()->findAvailableByIds($movieIds);
    }

    public function getTotalQuantityAvailable(int $movieId): int
    {
        $filme = FilmeRepository::getInstance()->findById($movieId);
        return $filme ? $filme->quantidade : 0;
    }

    public function decrementMovieStock(int $filmeId, int $quantidade = 1): bool
    {
        return FilmeRepository::getInstance()->decrementQuantity($filmeId, $quantidade);
    }

    public function incrementMovieStock(int $filmeId, int $quantidade = 1): bool
    {
        return FilmeRepository::getInstance()->incrementQuantity($filmeId, $quantidade);
    }

    public function attachMoviesToLocacao(int $locacaoId, array $filmes): void
    {
        foreach ($filmes as $filme) {
            $filmeId = is_object($filme) ? $filme->uuid : $filme;
            $quantidade = (is_object($filme) && isset($filme->quantidade)) ? $filme->quantidade : 1;

            $filmeModel = FilmeRepository::getInstance()->findByUuid($filmeId);
            if (!$filmeModel) {
                continue;
            }

            $this->model->create([
                'locacao_id' => $locacaoId,
                'filme_id' => $filmeModel->id,
                'quantidade' => $quantidade,
                'preco_unitario' => $filmeModel->valor_aluguel ?? 0,
            ]);

            $this->decrementMovieStock($filmeModel->id, $quantidade);
        }
    }

    public function detachMoviesFromLocacao(int $locacaoId, array $filmes): void
    {
        foreach ($filmes as $filmeId) {
            $filmeModel = FilmeRepository::getInstance()->findByUuid($filmeId);
            if (!$filmeModel) {
                continue;
            }

            $locacaoFilme = $this->model
                ->where('locacao_id', $locacaoId)
                ->where('filme_id', $filmeModel->id)
                ->first();

            if ($locacaoFilme) {
                $this->incrementMovieStock($locacaoFilme->filme_id, $locacaoFilme->quantidade);

                $locacaoFilme->delete();
            }
        }
    }

    public function calculateTotalValue(int $locacaoId): float
    {
        $total = $this->model
            ->where('locacao_id', $locacaoId)
            ->selectRaw('SUM(quantidade * preco_unitario) as total')
            ->value('total');

        return (float) ($total ?? 0);
    }

    public function updateLocacaoTotalValue(int $locacaoId): void
    {
        $total = $this->calculateTotalValue($locacaoId);

        Locacao::where('id', $locacaoId)->update(['valor_total' => $total]);
    }
}
