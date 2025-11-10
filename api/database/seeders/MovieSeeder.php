<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movies\Filme;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Inception',
            'sinopse' => 'Um filme sobre ladrões que invadem os sonhos.',
            'categoria' => 'Ficção Científica',
            'ano_lancamento' => 2010,
            'diretor' => 'Christopher Nolan',
            'valor_aluguel' => 10.00,
            'quantidade' => 5,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'The Shawshank Redemption',
            'sinopse' => 'A história de um homem preso injustamente.',
            'categoria' => 'Drama',
            'ano_lancamento' => 1994,
            'diretor' => 'Frank Darabont',
            'valor_aluguel' => 8.00,
            'quantidade' => 3,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'The Dark Knight',
            'sinopse' => 'Batman enfrenta seu maior inimigo, o Coringa.',
            'categoria' => 'Ação',
            'ano_lancamento' => 2008,
            'diretor' => 'Christopher Nolan',
            'valor_aluguel' => 12.00,
            'quantidade' => 4,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Pulp Fiction',
            'sinopse' => 'Histórias entrelaçadas de crime em Los Angeles.',
            'categoria' => 'Crime',
            'ano_lancamento' => 1994,
            'diretor' => 'Quentin Tarantino',
            'valor_aluguel' => 9.00,
            'quantidade' => 2,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Forrest Gump',
            'sinopse' => 'A vida extraordinária de um homem comum.',
            'categoria' => 'Drama',
            'ano_lancamento' => 1994,
            'diretor' => 'Robert Zemeckis',
            'valor_aluguel' => 7.00,
            'quantidade' => 6,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Interstellar',
            'sinopse' => 'Uma jornada através de buracos negros e galáxias distantes.',
            'categoria' => 'Ficção Científica',
            'ano_lancamento' => 2014,
            'diretor' => 'Christopher Nolan',
            'valor_aluguel' => 13.00,
            'quantidade' => 5,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'The Matrix',
            'sinopse' => 'Um hacker descobre a verdade sobre a realidade.',
            'categoria' => 'Ficção Científica',
            'ano_lancamento' => 1999,
            'diretor' => 'Lana Wachowski',
            'valor_aluguel' => 11.00,
            'quantidade' => 4,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Titanic',
            'sinopse' => 'Romance e tragédia no navio mais famoso do mundo.',
            'categoria' => 'Romance',
            'ano_lancamento' => 1997,
            'diretor' => 'James Cameron',
            'valor_aluguel' => 10.50,
            'quantidade' => 3,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Avatar',
            'sinopse' => 'Uma expedição em um mundo alienígena exótico.',
            'categoria' => 'Ficção Científica',
            'ano_lancamento' => 2009,
            'diretor' => 'James Cameron',
            'valor_aluguel' => 14.00,
            'quantidade' => 7,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Gladiator',
            'sinopse' => 'Um general romano se torna escravo e lutador.',
            'categoria' => 'Ação',
            'ano_lancamento' => 2000,
            'diretor' => 'Ridley Scott',
            'valor_aluguel' => 11.50,
            'quantidade' => 4,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'The Lion King',
            'sinopse' => 'A jornada de um jovem leão para reclamar seu reino.',
            'categoria' => 'Animação',
            'ano_lancamento' => 1994,
            'diretor' => 'Roger Allers',
            'valor_aluguel' => 8.50,
            'quantidade' => 8,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Frozen',
            'sinopse' => 'Duas irmãs em uma aventura mágica.',
            'categoria' => 'Animação',
            'ano_lancamento' => 2013,
            'diretor' => 'Chris Buck',
            'valor_aluguel' => 9.00,
            'quantidade' => 10,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Toy Story',
            'sinopse' => 'A amizade entre brinquedos que ganham vida.',
            'categoria' => 'Animação',
            'ano_lancamento' => 1995,
            'diretor' => 'John Lasseter',
            'valor_aluguel' => 7.50,
            'quantidade' => 6,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'The Avengers',
            'sinopse' => 'Heróis se unem para salvar o mundo.',
            'categoria' => 'Ação',
            'ano_lancamento' => 2012,
            'diretor' => 'Joss Whedon',
            'valor_aluguel' => 12.50,
            'quantidade' => 5,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'Jurassic Park',
            'sinopse' => 'Dinossauros ressuscitados em um parque temático.',
            'categoria' => 'Ação',
            'ano_lancamento' => 1993,
            'diretor' => 'Steven Spielberg',
            'valor_aluguel' => 11.00,
            'quantidade' => 4,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'The Shining',
            'sinopse' => 'Um homem isolado descende à loucura em um hotel vazio.',
            'categoria' => 'Terror',
            'ano_lancamento' => 1980,
            'diretor' => 'Stanley Kubrick',
            'valor_aluguel' => 10.00,
            'quantidade' => 2,
        ]);

        Filme::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'titulo' => 'The Exorcist',
            'sinopse' => 'Uma menina possuída e os padres que tentam salvá-la.',
            'categoria' => 'Terror',
            'ano_lancamento' => 1973,
            'diretor' => 'William Friedkin',
            'valor_aluguel' => 9.50,
            'quantidade' => 3,
        ]);
    }
}
