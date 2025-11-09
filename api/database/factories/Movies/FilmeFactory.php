<?php

namespace Database\Factories\Movies;

use App\Models\Movies\Filme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movies\Filme>
 */
class FilmeFactory extends Factory
{
    protected $model = Filme::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'id' => $this->faker->unique()->numberBetween(1, 100),
            'titulo' => $this->faker->sentence(3),
            'sinopse' => $this->faker->paragraph(),
            'diretor' => $this->faker->name(),
            'categoria' => $this->faker->randomElement(['Ação', 'Comédia', 'Drama', 'Terror', 'Ficção Científica']),
            'ano_lancamento' => $this->faker->year(),
            'valor_aluguel' => $this->faker->randomFloat(2, 5, 20),
            'quantidade' => $this->faker->numberBetween(1, 100),
        ];
    }
}
