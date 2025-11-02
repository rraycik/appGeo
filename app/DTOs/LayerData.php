<?php

namespace App\DTOs;

/**
 * Data Transfer Object para Layer
 * Isolando a estrutura de dados (SOLID - Dependency Inversion)
 */
readonly class LayerData
{
    public function __construct(
        public string $name,
        public ?array $geojson = null
    ) {
    }

    /**
     * Cria um DTO a partir de um array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            geojson: $data['geojson'] ?? null
        );
    }
}
