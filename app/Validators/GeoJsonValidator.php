<?php

namespace App\Validators;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Validator para GeoJSON
 * Seguindo o princípio Single Responsibility (SOLID)
 */
class GeoJsonValidator
{
    /**
     * Valida um array GeoJSON
     *
     * @param array $geojson
     * @return void
     * @throws InvalidArgumentException
     */
    public function validate(array $geojson): void
    {
        // Valida estrutura básica
        if (!isset($geojson['type'])) {
            throw new InvalidArgumentException('GeoJSON deve conter uma propriedade "type"');
        }

        // Valida tipo válido
        $validTypes = ['Feature', 'FeatureCollection', 'Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon', 'GeometryCollection'];
        
        if (!in_array($geojson['type'], $validTypes)) {
            throw new InvalidArgumentException('Tipo GeoJSON inválido: ' . $geojson['type']);
        }

        // Valida geometria
        if (isset($geojson['geometry'])) {
            $this->validateGeometry($geojson['geometry']);
        }

        // Valida usando PostGIS
        $this->validateWithPostGis($geojson);
    }

    /**
     * Valida a estrutura da geometria
     *
     * @param array $geometry
     * @return void
     * @throws InvalidArgumentException
     */
    private function validateGeometry(array $geometry): void
    {
        if (!isset($geometry['type']) || !isset($geometry['coordinates'])) {
            throw new InvalidArgumentException('Geometria deve conter "type" e "coordinates"');
        }
    }

    /**
     * Valida usando funções PostGIS
     *
     * @param array $geojson
     * @return void
     * @throws InvalidArgumentException
     */
    private function validateWithPostGis(array $geojson): void
    {
        try {
            $isValid = DB::selectOne(
                "SELECT ST_IsValid(ST_GeomFromGeoJSON(?)) as is_valid",
                [json_encode($geojson, JSON_UNESCAPED_UNICODE)]
            );

            if (!$isValid || !$isValid->is_valid) {
                throw new InvalidArgumentException('Geometria GeoJSON inválida ou não pode ser processada pelo PostGIS');
            }
        } catch (\Exception $e) {
            if ($e instanceof InvalidArgumentException) {
                throw $e;
            }
            
            throw new InvalidArgumentException('Erro ao validar geometria com PostGIS: ' . $e->getMessage());
        }
    }
}
