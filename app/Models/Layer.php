<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Layer
 * Mantendo apenas a estrutura de dados (SOLID - Single Responsibility)
 * Lógica de negócio está no Service Layer
 */
class Layer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Note: A geometria é gerenciada diretamente pelo Repository
     * para manter o Model simples e focado apenas na estrutura de dados
     */
}
