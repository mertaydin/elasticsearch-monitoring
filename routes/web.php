<?php

use ElasticsearchMonitoring\Http\Controllers\DashboardController;
use ElasticsearchMonitoring\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('elasticsearch-monitoring.dashboard.middleware', ['web', 'auth']))
    ->prefix(config('elasticsearch-monitoring.dashboard.path', 'monitoring/elasticsearch'))
    ->name('elasticsearch-monitoring.')
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/data', [DashboardController::class, 'data'])->name('data');
        Route::get('/indices', [IndexController::class, 'index'])->name('indices');
        Route::get('/indices/data', [IndexController::class, 'data'])->name('indices.data');
        Route::get('/indices/{index}/details', [IndexController::class, 'details'])->where('index', '[A-Za-z0-9_.-]+')->name('indices.details');
    });
