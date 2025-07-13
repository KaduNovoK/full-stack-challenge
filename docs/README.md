## Implementando o desafio full-stack-challenge

Este documento detalha o planejamento e a execução do desafio. Como mencionado no README principal do projeto, a separação entre proposta e solução visa realçar os contextos e manter o foco de leitura, evitando a mistura de objetivos distintos.

## Planejamento

Todo problema, por mais sucinto que seja, permite boas ou más decisões. Para este desafio, considerei as seguintes escolhas como mais adequadas ao contexto:

* Não usar separação por domínio segundo a Clean Architecture. Como as responsabilidades se limitavam à criação e listagem de `Tracks`, aplicar esse padrão seria overengineering.
* Evitar criar entidades `Track` vazias apenas com os ISRCs. Isso preveniria o banco de dados de conter registros em estado inválido. As faixas só são criadas após a obtenção bem-sucedida dos dados do Spotify.
* Utilizar jobs (ainda que de forma mínima e síncrona) para estabelecer uma base escalável e pronta para evoluir para processamento assíncrono.
* Armazenar o token de acesso do Spotify em cache, respeitando seu ciclo de vida e evitando chamadas desnecessárias.
* Armazenar o ISRC junto à `Track`, permitindo rastrear a correspondência entre a lista original e os dados obtidos.
* Optar por não usar Swagger, visto que havia apenas uma rota e esta era um get aberto, que o frontend resolveria diretamente em sua requisição.

## Instalação do desafio

URLs públicas disponíveis após a inicialização:

* [http://localhost:8081/](http://localhost:8081/) (phpMyAdmin)
* [http://localhost:8080/api/track](http://localhost:8080/api/track) (API)
* [http://localhost:4200/](http://localhost:4200/) (Frontend)

### Roteiro de execução (em `/docker`):

1. `docker compose build --no-cache`
2. `docker compose up -d`
3. `docker compose ps | grep fullstack` (para validar os containers)
4. `docker compose logs db -f` (aguarde o MySQL inicializar completamente)
5. Edite o `.env` em `/backend` com os dados do banco e as credenciais da API do Spotify:

   * `SPOTIFY_CLIENT_ID=...`
   * `SPOTIFY_CLIENT_SECRET=...`
6. `docker compose exec backend php artisan migrate`
7. `docker compose exec backend php artisan test`
8. `docker compose exec backend php artisan spotify:batch-fetch`
9. Acesse as rotas informadas.

## Divisão de contextos

Mesmo com repositório único, optei por separar os contextos para facilitar a visualização da estrutura:

* `/backend`
* `/frontend`
* `/docker`
* `/docs`

## Fluxo de desenvolvimento

1. Criar ambiente de desenvolvimento containerizado com Docker Compose.
2. Desenvolver o backend (API, integração com Spotify, persistência).
3. Desenvolver o frontend (listagem responsiva).
4. Documentar o processo.

## Stack Tecnológica

* PHP 8
* Composer 2
* Laravel 10
* Nginx
* MySQL 8
* phpMyAdmin
* Node.js 18
* Angular 12

## Escopos

### Docker (`/docker`)

Contém a base da aplicação, com destaque para:

#### Banco de Dados

```env
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=fullstack_db
MYSQL_USER=fullstack_user
MYSQL_PASSWORD=fullstack_secret
MYSQL_ROOT_HOST="%"
```

#### Portas

* backend: 9000
* nginx: 8080
* db: 3308
* phpmyadmin: 8081
* frontend: 4200

### Backend (`/backend`)

Instalação comum do Laravel com os seguintes conceitos aplicados:

#### Assemblers

Encapsula a resposta do Spotify (que já foi abstraida em DTOs) em um objeto próprio da aplicação.

#### Commands

* `spotify:fetch-track {isrc}`: usado para testes e exploração manual da resposta do Spotify e persistência.
* `spotify:batch-fetch`: percorre os 10 ISRCs do desafio, despacha jobs para tratar cada um.

#### DTOs

Transformam a resposta da API do Spotify em DTOs (Data Transfer Objects), permitindo maior clareza e controle sobre os dados. O mapeamento completo da resposta foi feito por experiência e exploração, não por necessidade direta do desafio.

#### Controllers

* `TrackController@index`: lista as faixas em ordem alfabética.
* `TrackController@show`: implementado, mas não utilizado no frontend.

#### Jobs

Processa cada ISRC, consulta o Spotify e cria a `Track` no banco.

#### Services

* `SpotifyService`: obtém o token, armazena em cache e consulta faixas.
* `TrackService`: centraliza a lógica de criação/atualização de faixas.

#### Logs

As respostas do Spotify são armazenadas em `storage/logs/spotify.log`. Observou-se que:

* Dois ISRCs não foram localizados.
* Todos os previews de áudio vieram como `null`.

#### Testes

Incluem testes unitários e de integração para garantir o funcionamento das classes criadas.

### Frontend (`/frontend`)

Listagem simples com Bootstrap. Como os dados eram poucos e os previews de áudio estavam indisponíveis, o detalhamento das faixas foi omitido.

### Docs (`/docs`)

Contém este documento explicativo.

## Possíveis Evoluções

* Nomeação de domínios locais: `api.onerpm.local` e `onerpm.local`.
* Documentação Swagger da API.
* Alternar entre views (lista/cards), filtros e paginação no frontend.

## Agradecimentos

Por fim, agradeço a oportunidade. Espero que a solução esteja à altura e contribua positivamente para o processo. Que Deus os abençoe.

-- Última atualização por Carlos em 13/07/2025