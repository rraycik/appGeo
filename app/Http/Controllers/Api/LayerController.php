<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LayerService;
use Illuminate\Http\JsonResponse;

/**
 * Controller API para Layers
 * Seguindo Single Responsibility - apenas gerencia requisições HTTP
 */
class LayerController extends Controller
{
    public function __construct(
        private LayerService $layerService
    ) {
    }

    /**
     * Get all layers as GeoJSON FeatureCollection
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = $this->layerService->getAllAsGeoJson();

        return response()->json($data);
    }
}
