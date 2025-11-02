<?php

namespace App\Repositories;

use App\Models\Layer;
use Illuminate\Support\Collection;

/**
 * Interface do Repository Pattern
 * Seguindo o princípio Dependency Inversion (SOLID)
 */
interface LayerRepositoryInterface
{
    /**
     * Busca todas as camadas
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Busca uma camada por ID
     *
     * @param int $id
     * @return Layer
     */
    public function findOrFail(int $id): Layer;

    /**
     * Cria uma nova camada
     *
     * @param array $data
     * @return Layer
     */
    public function create(array $data): Layer;

    /**
     * Atualiza uma camada
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Atualiza a geometria de uma camada
     *
     * @param int $id
     * @param array $geojson
     * @return void
     */
    public function updateGeometry(int $id, array $geojson): void;

    /**
     * Obtém a geometria como GeoJSON
     *
     * @param int $id
     * @return array|null
     */
    public function getGeometryAsGeoJson(int $id): ?array;

    /**
     * Remove uma camada
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
