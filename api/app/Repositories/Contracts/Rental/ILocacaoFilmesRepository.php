<?php

namespace App\Repositories\Contracts\Rental;

interface ILocacaoFilmesRepository
{
    public function validateMovieStock(array $movieIds): ?array;
    public function getAvailableMovies(array $movieIds);
    public function getTotalQuantityAvailable(int $movieId): int;
    public function decrementMovieStock(int $filmeId, int $quantidade = 1): bool;
    public function incrementMovieStock(int $filmeId, int $quantidade = 1): bool;
    public function attachMoviesToLocacao(int $locacaoId, array $filmes): void;
    public function detachMoviesFromLocacao(int $locacaoId, array $filmes): void;
    public function calculateTotalValue(int $locacaoId): float;
    public function updateLocacaoTotalValue(int $locacaoId): void;
}
