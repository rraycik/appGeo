<?php

use App\Http\Controllers\Api\LayerController;
use Illuminate\Support\Facades\Route;

Route::get('/layers', [LayerController::class, 'index'])->name('api.layers.index');