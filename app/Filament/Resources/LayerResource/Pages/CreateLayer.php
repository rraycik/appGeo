<?php

namespace App\Filament\Resources\LayerResource\Pages;

use App\DTOs\LayerData;
use App\Filament\Resources\LayerResource;
use App\Services\LayerService;
use Filament\Resources\Pages\CreateRecord;

/**
 * Page para criar Layers
 * Usando Service Layer para lógica de negócio (SOLID)
 */
class CreateLayer extends CreateRecord
{
    protected static string $resource = LayerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove fields that shouldn't be in the model
        unset($data['geojson_file']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $geojsonData = $this->form->getState()['geometry_data'] ?? null;
        
        if ($geojsonData) {
            $geojson = json_decode($geojsonData, true);
            
            // Usa o Service Layer para atualizar a geometria
            $layerService = app(LayerService::class);
            $layerData = new LayerData(
                name: $this->record->name,
                geojson: $geojson
            );
            
            $layerService->updateFromGeoJson($this->record->id, $layerData);
        }
    }
}
