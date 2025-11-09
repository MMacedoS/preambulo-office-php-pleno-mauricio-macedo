<?php

namespace Database\Factories\Rental;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental\LocacaoFilme>
 */
class LocacaoFilmeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locacao_id' => \App\Models\Rental\Locacao::factory(),
            'filme_id' => \App\Models\Movies\Filme::factory(),
            'quantidade' => $this->faker->numberBetween(1, 5),
            'preco_unitario' => $this->faker->randomFloat(2, 5, 50),
        ];
    }
}
