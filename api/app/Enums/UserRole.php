<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENTE = 'cliente';
    case ATENDENTE = 'atendente';
    case ADMINISTRADOR = 'administrador';

    public function label(): string
    {
        return match ($this) {
            self::CLIENTE => 'Cliente',
            self::ATENDENTE => 'Atendente',
            self::ADMINISTRADOR => 'Administrador',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::CLIENTE => [
                'ver_filmes',
                'criar_aluguel',
                'ver_meus_alugueis',
            ],
            self::ATENDENTE => [
                'ver_filmes',
                'gerenciar_alugueis',
                'ver_todos_alugueis',
                'ver_clientes',
                'criar_usuarios',
                'editar_usuarios',
            ],
            self::ADMINISTRADOR => [
                'ver_filmes',
                'gerenciar_filmes',
                'gerenciar_alugueis',
                'ver_todos_alugueis',
                'ver_clientes',
                'criar_usuarios',
                'editar_usuarios',
                'deletar_usuarios',
                'configurar_sistema',
                'ver_relatorios',
                'gerar_relatorios',
                'exportar_relatorios',
            ],
        };
    }
}
