# LocaFilmes

Sistema de locação de filmes.

LocaFilmes é uma aplicação web para gerenciar aluguel de filmes. Permite cadastro de clientes, controle de locações ativas, devoluções e gerenciamento de atrasos. Construído com Laravel (backend) e Vue.js (frontend).

## Executar o Projeto

Na raiz do projeto execute:

```bash
docker-compose up -d
```

Na pasta api, renomeie o arquivo `.env.example` para `.env`:

```bash
cp api/.env.example api/.env
```

Depois execute:

```bash
docker exec -it api composer install
```

Gere a chave da aplicação:

```bash
docker exec -it api php artisan key:generate
```

Execute as migrations:

```bash
docker exec -it api php artisan migrate
```

Execute os seeds:

```bash
docker exec -it api php artisan db:seed
```

Os seeds vão criar:

- Usuários (admin, atendentes, clientes)
- Filmes
- Locações atribuídas

Se necessário, ajuste as permissões da pasta storage:

```bash
docker exec -it api chmod -R 775 storage
```

## Acessar o Projeto

API Laravel: http://localhost:80

Front-end Vue: http://localhost:5173

Mailhog: http://localhost:1025

PgAdmin: http://localhost:5050

## Usuários para Teste

Admin Sistema

- Email: admin@gmail.com
- Papel: administrador

Pedro Cliente

- Email: pedro@gmail.com
- Papel: cliente

Ana Atendente

- Email: ana@gmail.com
- Papel: atendente

João Santos

- Email: joao@gmail.com
- Papel: cliente

Maria Araujo

- Email: atendente@gmail.com
- Papel: atendente

## Executar Testes

Na raiz do projeto execute:

```bash
./test.sh
```

Ou teste específico:

```bash
./test.sh tests/Feature/UserObserverTest.php
```

Via terminal do VS Code dentro do container:

```bash
php artisan test
```

## Estrutura

api/ - API Laravel

app/ - Aplicação Vue

docker/ - Configurações Docker

## Pastas do Projeto

```
---> api/
│   ---> app/
│   │   ---> Console/
│   │   ---> Enums/
│   │   ---> Http/
│   │   ---> Jobs/
│   │   ---> Mail/
│   │   ---> Models/
│   │   ---> Observers/
│   │   ---> Providers/
│   │   ---> Repositories/
│   │   ---> Services/
│   │   ---> Transformers/
│   │   ---> Traits/
│   ---> bootstrap/
│   ---> config/
│   ---> database/
│   │   ---> factories/
│   │   ---> migrations/
│   │   ---> seeders/
│   ---> public/
│   ---> resources/
│   ---> routes/
│   ---> storage/
│   ---> tests/
│   ---> vendor/
│   ---> artisan
---> app/
│   ---> src/
│   │   ---> assets/
│   │   ---> components/
│   │   ---> composables/
│   │   ---> constants/
│   │   ---> enums/
│   │   ---> pages/
│   │   ---> router/
│   │   ---> services/
│   │   ---> utils/
│   ---> public/
│   ---> package.json
---> docker/
    ---> nginx/
    ---> php/
    ---> supervisor/
```

## Tecnologias

Laravel 12 - Framework backend PHP

Vue 3 - Framework frontend JavaScript

JWT - Autenticação por token

PostgreSQL - Banco de dados

Docker - Containerização

Tailwind CSS - Estilização frontend
