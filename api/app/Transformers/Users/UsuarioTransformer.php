<?php

namespace App\Transformers\Users;

use App\Models\User;

class UsuarioTransformer
{
    public function transform(User $usuario)
    {
        return [
            'id' => $usuario->id,
            'name' => $usuario->name,
            'email' => $usuario->email,
            'role' => $usuario->role->value,
            'created_at' => $usuario->created_at,
            'updated_at' => $usuario->updated_at,
        ];
    }

    public function transformCollection($usuarios)
    {
        return array_map([$this, 'transform'], $usuarios->toArray());
    }
}
