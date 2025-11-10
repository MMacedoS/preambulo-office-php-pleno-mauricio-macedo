<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LoadEntityTrait;
use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Repositories\Traits\FilterSearchTrait;
use App\Transformers\Users\UsuarioTransformer;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use FilterSearchTrait, LoadEntityTrait;

    protected IUsuarioRepository $usuarioRepository;
    protected UsuarioTransformer $usuarioTransformer;
    protected $perPage = 2;

    public function __construct(
        IUsuarioRepository $usuarioRepository,
        UsuarioTransformer $usuarioTransformer
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->usuarioTransformer = $usuarioTransformer;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['name', 'email']);
        $filterData = $this->prepareFilters($filters);

        $users = $this->usuarioRepository->findAll(
            $filterData['criteria'],
            [],
            $filterData['orWhere']
        );

        $users = $this->usuarioTransformer->transformCollection($users);
        $paginated = $this->paginate($users, $perPage);

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
        if (!$this->isValidUuid($id)) {
            return response()->json(['message' => 'Usuário não encontrado'], 422);
        }

        $user = $this->usuarioRepository->findByUuid($id);

        if (is_null($user)) {
            return response()->json(['message' => 'Usuário não encontrado'], 422);
        }

        $user = $this->usuarioTransformer->transform($user);

        return response()->json(['data' => $user], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:administrador,atendente,cliente',
        ]);


        $newUser = $this->usuarioRepository->create($validated);

        if (!$newUser) {
            return response()->json(['message' => 'Falha ao criar usuario'], 500);
        }

        $newUser = $this->usuarioTransformer->transform($newUser);

        return response()->json(['data' => $newUser], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$this->isValidUuid($id)) {
            return response()->json(['message' => 'Usuário não encontrado'], 422);
        }

        $user = $this->usuarioRepository->findByUuid($id);

        if (is_null($user)) {
            return response()->json(['message' => 'Usuário não encontrado'], 422);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string|in:administrador,atendente,cliente',
        ]);

        $updatedUser = $this->usuarioRepository->update($user->id, $validated);

        if (!$updatedUser) {
            return response()->json(['message' => 'Usuário não encontrado ou não pôde ser atualizado'], 422);
        }

        $updatedUser = $this->usuarioTransformer->transform($updatedUser);

        return response()->json(['data' => $updatedUser], 200);
    }

    public function destroy(string $id)
    {
        if (!$this->isValidUuid($id)) {
            return response()->json(['message' => 'Usuário não encontrado'], 422);
        }

        $user = $this->usuarioRepository->findByUuid($id);
        if (is_null($user)) {
            return response()->json(['message' => 'Usuário não encontrado'], 422);
        }

        $deleted = $this->usuarioRepository->delete($user->id);

        if (!$deleted) {
            return response()->json(['message' => 'Usuário não encontrado ou não pôde ser deletado'], 422);
        }
        return response()->json(['message' => 'Usuário deletado com sucesso'], 204);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        $user = $this->usuarioTransformer->transform($user);

        return response()->json(['data' => $user], 200);
    }

    public function profileUpdate(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        $updatedUser = $this->usuarioRepository->update($user->id, $validated);

        if (!$updatedUser) {
            return response()->json(['message' => 'Usuário não encontrado ou não pôde ser atualizado'], 422);
        }

        $updatedUser = $this->usuarioTransformer->transform($updatedUser);

        return response()->json(['data' => $updatedUser], 200);
    }

    public function totalClients(Request $request)
    {
        $clients = $this->usuarioRepository->totalClients();

        return response()->json(
            [
                'data' => ['total_clients' => $clients]
            ],
            200
        );
    }
}
