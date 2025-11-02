# GeoApp

Aplicação de gestão de dados geográficos (PHP 8.4, Laravel 12, Filament 4) com:
- Painel administrativo em `/painel` (login do Filament)
- CRUD de camadas com upload de GeoJSON (validação PostGIS)
- Mapa na raiz `/` usando ArcGIS Maps SDK 4.x carregando camadas do banco via `GET /api/layers`

## Requisitos
- PHP 8.4+, Composer 2.x
- PostgreSQL com PostGIS
- Extensões PHP: pdo_pgsql, intl, zip, gd, mbstring, xml

## Setup rápido
1. Instalar dependências
```
composer install
```
2. Configurar `.env`
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=geoapp
DB_USERNAME=postgres
DB_PASSWORD=senha
```
3. Gerar APP_KEY e link de storage
```
php artisan key:generate
php artisan storage:link
```
4. Preparar banco (PostGIS e migrations)
- Garanta PostGIS instalado no banco
```
php artisan migrate
```
5. Criar usuário do painel
```
php artisan make:filament-user
```
6. Iniciar servidor
```
php artisan serve
```
Acesse: http://localhost:8000 (mapa) e http://localhost:8000/painel (admin)

## Observações
- A migration cria `layers` e habilita `postgis`. Índice GIST criado via SQL.
- Uploads salvos em `storage/app/public/geojson` (requer `storage:link`).
- API pública: `GET /api/layers` retorna FeatureCollection.
