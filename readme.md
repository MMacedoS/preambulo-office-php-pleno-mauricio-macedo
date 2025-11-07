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

> **Observação:**  
> Projeto de um sistema de locação para filmes
