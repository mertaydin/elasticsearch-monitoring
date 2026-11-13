<?php

use ElasticsearchMonitoring\Http\Controllers\DashboardController;
use ElasticsearchMonitoring\Http\Controllers\IndexController;
use ElasticsearchMonitoring\Http\Middleware\AuthorizeDashboard;
use Illuminate\Support\Facades\Route;

Route::middleware([...config('elasticsearch-monitoring.dashboard.middleware', ['web', 'auth']), AuthorizeDashboard::class])
    ->prefix(config('elasticsearch-monitoring.dashboard.path', 'monitoring/elasticsearch'))
    ->name('elasticsearch-monitoring.')
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/data', [DashboardController::class, 'data'])->name('data');
        Route::get('/indices', [IndexController::class, 'index'])->name('indices');
        Route::get('/indices/data', [IndexController::class, 'data'])->name('indices.data');
        Route::get('/indices/{index}/details', [IndexController::class, 'details'])->where('index', '[A-Za-z0-9_.-]+')->name('indices.details');\n        Route::get('/data-streams', [DataStreamController::class, 'index'])->name('data-streams');\n        Route::get('/data-streams/data', [DataStreamController::class, 'data'])->name('data-streams.data');\n        Route::get('/data-streams/{name}/details', [DataStreamController::class, 'details'])->where('name', '[A-Za-z0-9_.-]+')->name('data-streams.details');
    });
