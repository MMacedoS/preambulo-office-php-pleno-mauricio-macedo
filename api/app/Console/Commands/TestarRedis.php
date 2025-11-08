<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestarRedis extends Command
{
    protected $signature = 'redis:testar';
    protected $description = 'Testar conexão e funcionamento do Redis';

    public function handle()
    {
        try {
            Cache::put('teste_key', 'teste_valor', 60);
            $this->line('funcionando...');

            $valor = Cache::get('teste_key');
            if ($valor === 'teste_valor') {
                $this->line('funcionando...');
            }

            Cache::forever('teste_forever', 'valor_permanente');
            $this->line('funcionando... valor_permanente: ' . Cache::get('teste_forever'));

            Cache::forget('teste_key');
            $this->line('funcionando... teste_key removido');
        } catch (\Exception $e) {
            $this->error('Erro ao conectar ao Redis: ' . $e->getMessage());
        }
    }
}
