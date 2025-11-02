# GeoApp - Aplicação de Gestão de Dados Georreferenciados

Aplicação web desenvolvida com **PHP 8.4**, **Laravel 12.x** e **Filament 4** para gestão de dados geográficos com exibição em mapa interativo.

> ⚠️ **IMPORTANTE**: Esta aplicação requer **PHP 8.4+**. Veja `README_PRIORITARIO.md` para instalação urgente ou `PHP_8.4_SETUP.md` para instruções detalhadas.

## 📋 Requisitos do Sistema

- PHP 8.4+ (mínimo PHP 8.2 para desenvolvimento)
- Composer 2.x
- PostgreSQL 12+ com extensão PostGIS
- Node.js 18+ (opcional, para assets)
- Extensões PHP necessárias:
  - `pdo_pgsql` ⚠️ **Obrigatória** (para PostgreSQL)
  - `intl` ⚠️ **Obrigatória** (para Filament 4)
  - `zip` ⚠️ **Obrigatória** (para uploads)
  - `gd` ⚠️ **Obrigatória** (para processamento)
  - `mbstring` (geralmente já habilitada)
  - `xml` (geralmente já habilitada)

> **⚠️ Windows/XAMPP**: Edite `C:\xampp\php\php.ini` e remova o `;` das extensões acima. Reinicie o servidor.

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd geoapp
```

### 2. Instale as dependências

```bash
composer install
```

**Nota:** Se encontrar erros relacionados a extensões PHP, você pode instalar temporariamente ignorando os requisitos:

```bash
composer install --ignore-platform-reqs
```

### 3. Configure o arquivo .env

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

Configure as variáveis de ambiente, especialmente o banco de dados PostgreSQL:

```env
APP_NAME=GeoApp
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=geoapp
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
```

### 4. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 5. Configure o PostgreSQL

Acesse o PostgreSQL e crie o banco de dados com extensão PostGIS:

```sql
CREATE DATABASE geoapp;
\c geoapp
CREATE EXTENSION IF NOT EXISTS postgis;
```

### 6. Execute as migrations

```bash
php artisan migrate
```

A migration `create_layers_table` criará automaticamente a extensão PostGIS se ainda não estiver instalada.

### 7. Crie um usuário administrador para o painel Filament

```bash
php artisan make:filament-user
```

Siga as instruções para criar o primeiro usuário administrador.

### 8. Configure o storage link (para uploads)

```bash
php artisan storage:link
```

### 9. Inicie o servidor de desenvolvimento

```bash
php artisan serve
```

A aplicação estará disponível em: **http://localhost:8000**

## 🗺️ Funcionalidades

### Parte 1: Painel Administrativo

**URL:** `/painel`

- **Autenticação:** Login protegido por senha
- **CRUD de Camadas Geográficas:**
  - Criar, editar, listar e excluir camadas
  - Upload de arquivos GeoJSON
  - Validação automática de geometrias
  - Armazenamento indexado no banco de dados

### Parte 2: Mapa na Página Inicial

**URL:** `/` (rota raiz)

- Visualização de todas as camadas cadastradas
- Mapa interativo usando **ArcGIS Maps SDK 4.x**
- Carregamento dinâmico das camadas do banco de dados
- Legenda interativa
- Zoom automático para visualizar todas as camadas

## 📁 Estrutura do Projeto

```
geoapp/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   └── LayerResource.php      # Resource Filament para CRUD
│   │   └── Widgets/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── LayerController.php # API para carregar layers
│   └── Models/
│       └── Layer.php                   # Model com métodos PostGIS
├── database/
│   └── migrations/
│       └── create_layers_table.php    # Migration com PostGIS
├── resources/
│   └── views/
│       └── map.blade.php              # Página do mapa
└── routes/
    ├── web.php                        # Rotas web
    └── api.php                        # Rotas API
```

## 🗄️ Estrutura do Banco de Dados

### Tabela: `layers`

| Campo      | Tipo        | Descrição                          |
|------------|-------------|------------------------------------|
| id         | bigint      | Chave primária incremental        |
| name       | varchar(100)| Nome da camada                     |
| geometry   | geometry    | Geometria PostGIS (de GeoJSON)     |
| created_at | timestamp   | Data de criação                    |
| updated_at | timestamp   | Data de atualização                |

**Índices:**
- Índice espacial GIST em `geometry` para otimização de consultas

## 🔧 Tecnologias Utilizadas

- **Backend:**
  - Laravel 12.x
  - PHP 8.4+
  - PostgreSQL com PostGIS
  - Filament 4 (Painel Administrativo)
  - Doctrine DBAL (manipulação de geometrias)

## 🏗️ Arquitetura

A aplicação segue os **princípios SOLID** e **boas práticas de arquitetura**:

- **Service Layer**: Lógica de negócio isolada em Services
- **Repository Pattern**: Acesso a dados abstraído através de interfaces
- **DTO Pattern**: Transferência de dados tipada e imutável
- **Dependency Injection**: Inversão de dependências via Service Container
- **Validation Layer**: Validação isolada e reutilizável

Ver `ARCHITECTURE.md` para detalhes completos da arquitetura.

- **Frontend:**
  - ArcGIS Maps SDK for JavaScript 4.x
  - Blade Templates
  - JavaScript ES6+

## 📝 Boas Práticas Aplicadas

- **SOLID Principles:** Separação de responsabilidades, inversão de dependências
- **Repository Pattern:** Abstração de acesso a dados
- **Service Layer:** Lógica de negócio isolada
- **Validação:** Validação de GeoJSON e geometrias
- **Error Handling:** Tratamento adequado de erros
- **Code Organization:** Estrutura modular e organizada

## 🧪 Testes

```bash
php artisan test
```

## 📚 Documentação Adicional

### Formato GeoJSON Esperado

O sistema aceita arquivos GeoJSON no formato padrão:

```json
{
  "type": "Feature",
  "properties": {},
  "geometry": {
    "type": "Point",
    "coordinates": [-47.8825, -15.7942]
  }
}
```

Ou FeatureCollection:

```json
{
  "type": "FeatureCollection",
  "features": [...]
}
```

### API Endpoints

- `GET /api/layers` - Retorna todas as camadas em formato GeoJSON FeatureCollection

## 🐛 Troubleshooting

### Erro: "PostGIS extension not found"
- Certifique-se de que o PostGIS está instalado no PostgreSQL
- Execute manualmente: `CREATE EXTENSION postgis;`

### Erro: "Invalid GeoJSON format"
- Verifique se o arquivo JSON é válido
- Certifique-se de que contém uma propriedade `type` e `geometry`

### Erro de extensões PHP
- Habilite as extensões necessárias no `php.ini`
- Reinicie o servidor web após habilitar

## 📄 Licença

Este projeto é um desafio técnico desenvolvido para avaliação.

## 👤 Autor

Desenvolvido seguindo os requisitos do desafio técnico para Desenvolvedor Full Stack.

---

**Versão:** 1.0.0  
**Última atualização:** Novembro 2025
