<?php

use ElasticsearchMonitoring\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('elasticsearch-monitoring.dashboard.middleware', ['web', 'auth']))
    ->prefix(config('elasticsearch-monitoring.dashboard.path', 'monitoring/elasticsearch'))
    ->name('elasticsearch-monitoring.')
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/data', [DashboardController::class, 'data'])->name('data');
    });