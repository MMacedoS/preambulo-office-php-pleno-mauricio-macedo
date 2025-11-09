<?php

namespace App\Repositories\Entities\Rental;

use App\Models\Rental\Locacao;
use App\Models\Rental\LocacaoFilme;
use App\Repositories\Contracts\Rental\ILocacaoFilmesRepository;
use App\Repositories\Contracts\Movies\IFilmeRepository;
use App\Repositories\Traits\CacheTrait;
use App\Repositories\Traits\SingletonTrait;

class LocacaoFilmesRepository implements ILocacaoFilmesRepository
{
    use SingletonTrait, CacheTrait;

    protected $model;
    protected IFilmeRepository $filmeRepository;

    public function __construct()
    {
        $this->model = new LocacaoFilme();
    }

    public function validateMovieStock(array $movieIds): ?array
    {
        $filmeRepository = $this->getFilmeRepository();
        $moviesWithInsufficientStock = $filmeRepository->findByIdsWithInsufficientStock($movieIds);
        return empty($moviesWithInsufficientStock) ? null : $moviesWithInsufficientStock;
    }

    public function getAvailableMovies(array $movieIds)
    {
        $filmeRepository = $this->getFilmeRepository();
        return $filmeRepository->findAvailableByIds($movieIds);
    }

    public function getTotalQuantityAvailable(int $movieId): int
    {
        $filmeRepository = $this->getFilmeRepository();
        $filme = $filmeRepository->findById($movieId);
        return $filme ? $filme->quantidade : 0;
    }

    public function decrementMovieStock(int $filmeId, int $quantidade = 1): bool
    {
        $filmeRepository = $this->getFilmeRepository();
        return $filmeRepository->decrementQuantity($filmeId, $quantidade);
    }

    public function incrementMovieStock(int $filmeId, int $quantidade = 1): bool
    {
        $filmeRepository = $this->getFilmeRepository();
        return $filmeRepository->incrementQuantity($filmeId, $quantidade);
    }

    public function attachMoviesToLocacao(int $locacaoId, array $filmes): void
    {
        $filmeRepository = $this->getFilmeRepository();

        foreach ($filmes as $filme) {
            $filmeId = is_object($filme) ? $filme->id : $filme;
            $quantidade = (is_object($filme) && isset($filme->quantidade)) ? $filme->quantidade : 1;

            $filmeModel = $filmeRepository->findById($filmeId);
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
            $locacaoFilme = $this->model
                ->where('locacao_id', $locacaoId)
                ->where('filme_id', $filmeId)
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

    protected function getFilmeRepository(): IFilmeRepository
    {
        if (!isset($this->filmeRepository)) {
            $this->filmeRepository = app()->make(IFilmeRepository::class);
        }
        return $this->filmeRepository;
    }
}
