<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Repositories\Traits\FilterSearchTrait;
use App\Transformers\Users\UsuarioTransformer;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use FilterSearchTrait;

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
        $user = $this->usuarioRepository->findByUuid($id);

        if (is_null($user)) {
            return response()->json(['message' => 'Usuario não encontrado'], 422);
        }

        return response()->json(['data' => $user], 200);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'email', 'password', 'role']);
        $newUser = $this->usuarioRepository->create($data);
        return response()->json(['data' => $newUser], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'email', 'role']);
        $updatedUser = $this->usuarioRepository->update($id, $data);
        if (!$updatedUser) {
            return response()->json(['message' => 'User not found or could not be updated'], 404);
        }
        return response()->json(['data' => $updatedUser], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->usuarioRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'User not found or could not be deleted'], 404);
        }
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
