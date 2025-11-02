<?php

namespace App\Repositories;

use App\Models\Layer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Implementação do Repository Pattern
 * Isolando a lógica de acesso a dados (SOLID - Single Responsibility)
 */
class LayerRepository implements LayerRepositoryInterface
{
    public function all(): Collection
    {
        return Layer::all();
    }

    public function findOrFail(int $id): Layer
    {
        return Layer::findOrFail($id);
    }

    public function create(array $data): Layer
    {
        return Layer::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Layer::where('id', $id)->update($data);
    }

    public function updateGeometry(int $id, array $geojson): void
    {
        $geojsonString = json_encode($geojson, JSON_UNESCAPED_UNICODE);
        
        DB::table('layers')
            ->where('id', $id)
            ->update([
                'geometry' => DB::raw("ST_GeomFromGeoJSON('" . addslashes($geojsonString) . "')")
            ]);
    }

    public function getGeometryAsGeoJson(int $id): ?array
    {
        $result = DB::selectOne(
            "SELECT ST_AsGeoJSON(geometry) as geojson FROM layers WHERE id = ?",
            [$id]
        );

        if (!$result || !$result->geojson) {
            return null;
        }

        return json_decode($result->geojson, true);
    }

    public function delete(int $id): bool
    {
        return Layer::where('id', $id)->delete();
    }
}
