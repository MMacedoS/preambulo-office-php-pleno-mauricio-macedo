<?php

namespace App\Http\Controllers\movies;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LoadEntityTrait;
use App\Repositories\Contracts\Movies\IFilmeRepository;
use App\Repositories\Traits\FilterSearchTrait;
use App\Transformers\Movies\FilmeTransformer;
use Illuminate\Http\Request;

class FilmeController extends Controller
{
    use LoadEntityTrait;

    protected IFilmeRepository $filmeRepository;
    protected FilmeTransformer $filmeTransformer;

    public function __construct(IFilmeRepository $filmeRepository, FilmeTransformer $filmeTransformer)
    {
        $this->filmeRepository = $filmeRepository;
        $this->filmeTransformer = $filmeTransformer;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $orderBy = $request->get('orderBy', ['created_at' => 'desc']);
        $filters = $request->except(['per_page', 'orderBy', 'page']);

        $filterData = $this->prepareFilters($filters);

        $filmes = $this->filmeRepository->findAll($filterData['criteria'], $orderBy, $filterData['orWhere']);
        $transformed = $this->filmeTransformer->transformCollection($filmes);
        $paginated = $this->paginate($transformed, $perPage);

        return response()->json(
            [
                'data' => $paginated->items(),
                'meta' => $this->getPaginationMeta($paginated)
            ],
            200
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'genre' => 'required|string|max:100',
            'release_year' => 'required|integer|min:1888|max:' . date('Y'),
            'rental_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $filmeData = $this->filmeTransformer->transformArray($validated);

        $filme = $this->filmeRepository->create($filmeData);

        if (!$filme) {
            return response()->json(['message' => 'Falha ao criar filme'], 500);
        }

        $transformed = $this->filmeTransformer->transform($filme);

        return response()->json($transformed, 201);
    }

    public function update(Request $request, string $id)
    {
        if (!$this->isValidUuid($id)) {
            return response()->json(['message' => 'Filme não encontrado'], 404);
        }

        $filme = $this->filmeRepository->findByUuid($id);

        if (is_null($filme)) {
            return response()->json(['message' => 'Filme não encontrado'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'genre' => 'sometimes|required|string|max:100',
            'release_year' => 'sometimes|required|integer|min:1888|max:' . date('Y'),
            'rental_price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|required|integer|min:0',
        ]);

        $filmeData = $this->filmeTransformer->transformArray($validated);

        $updated = $this->filmeRepository->update($filme->id, $filmeData);

        if (!$updated) {
            return response()->json(['message' => 'Falha ao atualizar filme'], 500);
        }

        $transformed = $this->filmeTransformer->transform($updated);

        return response()->json($transformed, 200);
    }

    public function destroy(string $id)
    {
        if (!$this->isValidUuid($id)) {
            return response()->json(['message' => 'Filme não encontrado'], 422);
        }

        $filme = $this->filmeRepository->findByUuid($id);

        if (is_null($filme)) {
            return response()->json(['message' => 'Filme não encontrado'], 422);
        }

        $deleted = $this->filmeRepository->delete($filme->id);

        if (!$deleted) {
            return response()->json(['message' => 'Falha ao deletar filme'], 500);
        }

        return response()->json(['message' => 'Filme deletado com sucesso'], 204);
    }
}
