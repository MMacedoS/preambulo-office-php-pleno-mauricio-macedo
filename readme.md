# Estrutura do Projeto

```
├-- api/                 # API Laravel
│   |-- app/
│   |-- bootstrap/
│   |-- config/
│   |-- database/
│   |-- public/
│   |-- resources/
│   |-- routes/
│   |-- storage/
│   |-- tests/
│   |-- artisan
│   |-- composer.json
│
|-- app/                # Aplicação Web
│   ├-- src/
│   ├-- public/
│   ├-- node_modules/
│   ├-- package.json
│   └-- vite.config.js
│
├-- docker/                  # Arquivos Docker
│   ├-- nginx/
│   ├-- php/
│   └-- docker-compose.yml
│
|-- test.sh             # Script para executar testes
|-- README.md

```

> **Rodar o projeto:**  
> No terminal ou cmd execute o `docker-compose up -d`;

> No terminal ou cmd execute o `docker exec -it app composer install`;

> No terminal ou cmd execute o `docker exec -it app php artisan key:generate`;

> No terminal ou cmd execute o `docker exec -it app php artisan migrate`;

> No terminal ou cmd execute o `docker exec -it app php artisan migrate`;

> **Acessar o projeto:**  
> acessar api `http://localhost:80`;
> acessar app `http://localhost:5173`;

---

## Como Executar Testes

### Via Terminal Externo (Recomendado):

```bash
# Na raiz do projeto
./test.sh                                    # Executa TODOS os testes
./test.sh tests/Feature/UserObserverTest.php # Executa teste específico
```

### Via Terminal VS Code:

```bash
php artisan test                                   # Todos os testes
php artisan test tests/Feature/UserObserverTest.php
```

### Testes Disponíveis:

- `UserObserverTest` - Testa criação de usuários e observer (5 testes)

## Notas sobre o Ambiente

- PHP: Herd Lite (`/home/void/.config/herd-lite/bin/php`)
- PHPUnit: v11.5.43
- Banco de Testes: SQLite (`storage/testing.sqlite`)
- RefreshDatabase: Ativo (dados temporários por teste)

---

> **Observação:**  
> Projeto de um sistema de locação para filmes
