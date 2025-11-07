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

> **rodar o projeto:**  
> no terminal ou cmd execute o `docker-compose up -d`;

> no terminal ou cmd execute o `docker exec -it app composer install`;

> no terminal ou cmd execute o `docker exec -it app php artisan key:generate`;

> no terminal ou cmd execute o `docker exec -it app php artisan migrate`;

---

> **Observação:**  
> projeto de um sistema de locação para filmes
