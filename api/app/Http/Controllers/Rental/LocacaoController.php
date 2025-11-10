<?php

namespace App\Http\Controllers\Rental;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Rental\ILocacaoRepository;
use App\Repositories\Contracts\Rental\ILocacaoFilmesRepository;
use App\Transformers\Rental\LocacaoTransformer;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    protected ILocacaoRepository $locacaoRepository;
    protected ILocacaoFilmesRepository $locacaoFilmesRepository;
    protected LocacaoTransformer $locacaoTransformer;

    public function __construct(
        ILocacaoRepository $locacaoRepository,
        ILocacaoFilmesRepository $locacaoFilmesRepository,
        LocacaoTransformer $locacaoTransformer
    ) {
        $this->locacaoRepository = $locacaoRepository;
        $this->locacaoFilmesRepository = $locacaoFilmesRepository;
        $this->locacaoTransformer = $locacaoTransformer;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $orderBy = $request->get('orderBy', ['created_at' => 'desc']);
        $filters = $request->except(['per_page', 'orderBy', 'page']);

        $filterData = $this->prepareFilters($filters);

        $locacoes = $this->locacaoRepository->findAll($filterData['criteria'], $orderBy, $filterData['orWhere']);
        $transformed = $this->locacaoTransformer->transformCollection($locacoes);
        $paginated = $this->paginate($transformed, $perPage);

        return response()->json(
            [
                'data' => $paginated->items(),
                'meta' => $this->getPaginationMeta($paginated)
            ],
            200
        );
    }

    public function show(Request $request, string $id)
    {
        $locacao = $this->loadRentalByUuid($id);
        if (is_null($locacao)) {
            return response()->json(['message' => 'Locação não encontrada'], 422);
        }

        $transformed = $this->locacaoTransformer->transformRental($locacao);
        return response()->json(['data' => $transformed], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|string|exists:users,uuid',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
            'movies' => 'required|array|min:1',
            'movies.*' => 'integer|exists:filmes,id',
            'status' => 'required|string|in:ativo,devolvido,atrasado',
            'total_value' => 'required|numeric|min:0',
        ]);

        $insufficientStockMovies = $this->locacaoFilmesRepository->validateMovieStock($validatedData['movies']);

        if ($insufficientStockMovies !== null) {
            return response()->json([
                'message' => 'Validação falhou',
                'errors' => [
                    'movies' => ['Um ou mais filmes não possuem quantidade disponível para aluguel.']
                ]
            ], 422);
        }

        $dataTransformed = $this->locacaoTransformer->transformArray($validatedData);

        $usuarioId = $this->loadUserByUuid($validatedData['client_id']);
        if ($usuarioId) {
            $dataTransformed['usuario_id'] = $usuarioId;
        }

        $dataTransformed['valor_total'] = 0;

        $locacao = $this->locacaoRepository->create($dataTransformed);

        if (is_null($locacao)) {
            return response()->json(['message' => 'Erro ao criar a locação.'], 422);
        }

        $this->locacaoFilmesRepository->updateLocacaoTotalValue($locacao->id);

        $locacao->refresh();

        $transformed = $this->locacaoTransformer->transform($locacao);
        return response()->json(['data' => $transformed], 201);
    }

    public function update(Request $request, $id)
    {
        $locacao = $this->loadRentalByUuid($id);
        if (is_null($locacao)) {
            return response()->json(['message' => 'Locação não encontrada'], 422);
        }

        $validatedData = $request->validate([
            'client_id' => 'required|string|exists:users,uuid',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
            'movies' => 'required|array|min:1',
            'movies.*' => 'integer|exists:filmes,id',
            'status' => 'required|string|in:ativo,devolvido,atrasado',
            'total_value' => 'required|numeric|min:0',
        ]);

        $insufficientStockMovies = $this->locacaoFilmesRepository->validateMovieStock($validatedData['movies']);

        if ($insufficientStockMovies !== null) {
            return response()->json([
                'message' => 'Validação falhou',
                'errors' => [
                    'movies' => ['Um ou mais filmes não possuem quantidade disponível para aluguel.']
                ]
            ], 422);
        }

        $dataTransformed = $this->locacaoTransformer->transformArray($validatedData);

        $usuarioId = $this->loadUserByUuid($validatedData['client_id']);
        if ($usuarioId) {
            $dataTransformed['usuario_id'] = $usuarioId;
        }

        $locacao = $this->locacaoRepository->update($locacao->id, $dataTransformed);

        if (is_null($locacao)) {
            return response()->json(['message' => 'Erro ao atualizar a locação.'], 422);
        }

        $this->locacaoFilmesRepository->updateLocacaoTotalValue($locacao->id);

        $locacao->refresh();

        $transformed = $this->locacaoTransformer->transform($locacao);
        return response()->json(['data' => $transformed], 200);
    }

    public function destroy($id)
    {
        $locacao = $this->loadRentalByUuid($id);
        if (is_null($locacao)) {
            return response()->json(['message' => 'Locação não encontrada'], 422);
        }

        $deleted = $this->locacaoRepository->delete($locacao->id);

        if ($deleted === false) {
            return response()->json([
                'message' => 'Não é possível deletar uma locação com filmes associados',
            ], 422);
        }

        if (!$deleted) {
            return response()->json(['message' => 'Erro ao deletar a locação.'], 422);
        }

        return response()->json([], 204);
    }

    public function effectuateReturn(Request $request, $locacaoId)
    {
        $locacao = $this->loadRentalByUuid($locacaoId);
        if (is_null($locacao)) {
            return response()->json(['message' => 'Locação não encontrada'], 422);
        }

        $this->locacaoRepository->processReturn($locacao->id);

        return response()->json(['message' => 'Devolução efetuada com sucesso.'], 200);
    }

    public function attachMovies(Request $request, $locacaoId)
    {
        $filmes = $request->input('filmes', []);
        $this->locacaoFilmesRepository->attachMoviesToLocacao($locacaoId, $filmes);

        return response()->json(['message' => 'Filmes anexados com sucesso.']);
    }

    public function detachMovies(Request $request, $locacaoId)
    {
        $filmes = $request->input('filmes', []);
        $this->locacaoFilmesRepository->detachMoviesFromLocacao($locacaoId, $filmes);

        return response()->json(['message' => 'Filmes desanexados com sucesso.']);
    }

    public function updateTotalValue($locacaoId)
    {
        $this->locacaoFilmesRepository->updateLocacaoTotalValue($locacaoId);

        return response()->json(['message' => 'Valor total da locação atualizado com sucesso.']);
    }

    public function calculateTotalValue($locacaoId)
    {
        $totalValue = $this->locacaoFilmesRepository->calculateTotalValue($locacaoId);

        return response()->json(['total_value' => $totalValue]);
    }

    public function rentalActiveAndLateReturns(Request $request)
    {
        $user = $request->user();
        $rentalsStats = $this->locacaoRepository->getClientActiveRentals($user->id);

        $rentalsStats = $this->locacaoTransformer->transformCollection($rentalsStats);

        return response()->json(['data' => $rentalsStats], 200);
    }

    public function rentalHistory(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $orderBy = $request->get('orderBy', ['created_at' => 'desc']);
        $filters = $request->except(['per_page', 'orderBy', 'page']);

        $filterData = $this->prepareFilters($filters);

        $filterData['criteria']['usuario_id'] = $user->id;

        $locacoes = $this->locacaoRepository->findAll($filterData['criteria'], $orderBy, $filterData['orWhere']);
        $transformed = $this->locacaoTransformer->transformCollection($locacoes);
        $paginated = $this->paginate($transformed, $perPage);

        return response()->json(
            [
                'data' => $paginated->items(),
                'meta' => $this->getPaginationMeta($paginated)
            ],
            200
        );
    }
}
