<?php

namespace Database\Factories\Rental;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental\Locacao>
 */
class LocacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'id' => $this->faker->unique()->randomNumber(),
            'usuario_id' => \App\Models\User::factory(),
            'data_inicio' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'data_devolucao' => $this->faker->dateTimeBetween('now', '+1 month'),
            'valor_total' => $this->faker->randomFloat(2, 10, 200),
            'status' => $this->faker->randomElement(['em_andamento', 'concluida', 'atrasada']),
            'multa' => $this->faker->randomFloat(2, 0, 50),
        ];
    }
}
