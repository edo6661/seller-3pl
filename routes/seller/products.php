<?php

use App\Http\Controllers\Seller\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->name('seller.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])
        ->middleware('teamPermission:products.view')
        ->name('index');
        
    Route::get('/create', [ProductController::class, 'create'])
        ->middleware('teamPermission:products.create')
        ->name('create');
        
    Route::post('/', [ProductController::class, 'store'])
        ->middleware('teamPermission:products.create')
        ->name('store');
        
    Route::get('/export', [ProductController::class, 'export'])
        ->middleware('teamPermission:products.view')
        ->name('export');
        
    Route::post('/export-selected', [ProductController::class, 'exportSelected'])
        ->middleware('teamPermission:products.view')
        ->name('export-selected');
        
    Route::post('/bulk-delete', [ProductController::class, 'bulkDelete'])
        ->middleware('teamPermission:products.delete')
        ->name('bulk-delete');
        
    Route::post('/bulk-toggle-status', [ProductController::class, 'bulkToggleStatus'])
        ->middleware('teamPermission:products.edit')
        ->name('bulk-toggle-status');
        
    Route::get('/{id}', [ProductController::class, 'show'])
        ->middleware('teamPermission:products.view')
        ->name('show');
        
    Route::get('/{id}/edit', [ProductController::class, 'edit'])
        ->middleware('teamPermission:products.edit')
        ->name('edit');
        
    Route::put('/{id}', [ProductController::class, 'update'])
        ->middleware('teamPermission:products.edit')
        ->name('update');
        
    Route::delete('/{id}', [ProductController::class, 'destroy'])
        ->middleware('teamPermission:products.delete')
        ->name('destroy');
        
    Route::patch('/{id}/toggle-status', [ProductController::class, 'toggleStatus'])
        ->middleware('teamPermission:products.edit')
        ->name('toggle-status');
});
