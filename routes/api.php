<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\SessionController;

// Agrupar las rutas bajo el prefijo api/v1/dnda
Route::prefix('v1')->group(function () {
    
    // Rutas GET
    Route::get('/files/download', [FileController::class, 'download']);
    Route::get('/folder/exists', [FolderController::class, 'exists']);
    
    // Rutas POST
    Route::post('/folders/create', [FolderController::class, 'create']);
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::post('/session/login', [SessionController::class, 'login']);
});
