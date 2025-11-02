<?php

namespace App\Services;

use App\DTOs\LayerData;
use App\Models\Layer;
use App\Repositories\LayerRepositoryInterface;
use App\Validators\GeoJsonValidator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service Layer para gerenciar camadas geográficas
 * Seguindo o princípio Single Responsibility Principle (SOLID)
 */
class LayerService
{
    public function __construct(
        private LayerRepositoryInterface $repository,
        private GeoJsonValidator $validator
    ) {
    }

    /**
     * Cria uma nova camada a partir de dados GeoJSON
     *
     * @param LayerData $data
     * @return Layer
     * @throws InvalidArgumentException
     */
    public function createFromGeoJson(LayerData $data): Layer
    {
        // Valida o GeoJSON antes de processar
        $this->validator->validate($data->geojson);

        // Cria o registro sem geometria primeiro
        $layer = $this->repository->create([
            'name' => $data->name,
        ]);

        // Atualiza com a geometria validada
        $this->repository->updateGeometry($layer->id, $data->geojson);

        return $layer->fresh();
    }

    /**
     * Atualiza uma camada existente
     *
     * @param int $id
     * @param LayerData $data
     * @return Layer
     * @throws InvalidArgumentException
     */
    public function updateFromGeoJson(int $id, LayerData $data): Layer
    {
        $layer = $this->repository->findOrFail($id);

        // Valida o GeoJSON se fornecido
        if ($data->geojson !== null) {
            $this->validator->validate($data->geojson);
            $this->repository->updateGeometry($id, $data->geojson);
        }

        // Atualiza o nome se fornecido
        if ($data->name !== null) {
            $this->repository->update($id, ['name' => $data->name]);
        }

        return $layer->fresh();
    }

    /**
     * Obtém todas as camadas como GeoJSON FeatureCollection
     *
     * @return array
     */
    public function getAllAsGeoJson(): array
    {
        $layers = $this->repository->all();
        $features = [];

        foreach ($layers as $layer) {
            $geometry = $this->repository->getGeometryAsGeoJson($layer->id);

            if ($geometry !== null) {
                $features[] = [
                    'type' => 'Feature',
                    'id' => $layer->id,
                    'properties' => [
                        'id' => $layer->id,
                        'name' => $layer->name,
                        'created_at' => $layer->created_at->toIso8601String(),
                    ],
                    'geometry' => $geometry,
                ];
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    /**
     * Remove uma camada
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
