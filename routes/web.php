<?php

use ElasticsearchMonitoring\Http\Controllers\AlertHistoryController;
use ElasticsearchMonitoring\Http\Controllers\ApiRequestHistoryController;
use ElasticsearchMonitoring\Http\Controllers\CapacityController;
use ElasticsearchMonitoring\Http\Controllers\DashboardController;
use ElasticsearchMonitoring\Http\Controllers\DashboardNoteController;
use ElasticsearchMonitoring\Http\Controllers\ConnectionStatusController;
use ElasticsearchMonitoring\Http\Controllers\IndexController;
use ElasticsearchMonitoring\Http\Controllers\NodeController;
use ElasticsearchMonitoring\Http\Controllers\QueryActivityController;
use ElasticsearchMonitoring\Http\Controllers\ShardController;
use ElasticsearchMonitoring\Http\Controllers\SnapshotController;
use ElasticsearchMonitoring\Http\Controllers\ThreadPoolController;
use ElasticsearchMonitoring\Http\Middleware\AuthorizeDashboard;
use Illuminate\Support\Facades\Route;

Route::middleware([...config('elasticsearch-monitoring.dashboard.middleware', ['web', 'auth']), AuthorizeDashboard::class])
    ->prefix(config('elasticsearch-monitoring.dashboard.path', 'monitoring/elasticsearch'))
    ->name('elasticsearch-monitoring.')
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/data', [DashboardController::class, 'data'])->name('data');
        Route::get('/connection', [ConnectionStatusController::class, 'show'])->name('connection');
        Route::get('/notes', [DashboardNoteController::class, 'index'])->name('notes.index');
        Route::post('/notes', [DashboardNoteController::class, 'store'])->name('notes.store');
        Route::patch('/notes/{note}', [DashboardNoteController::class, 'update'])->whereNumber('note')->name('notes.update');
        Route::delete('/notes/{note}', [DashboardNoteController::class, 'destroy'])->whereNumber('note')->name('notes.destroy');
        Route::get('/indices', [IndexController::class, 'index'])->name('indices');
        Route::get('/indices/data', [IndexController::class, 'data'])->name('indices.data');
        Route::get('/indices/{index}/indexing-stats', [IndexController::class, 'indexingStats'])->where('index', '[A-Za-z0-9_.-]+')->name('indices.indexing-stats');
        Route::get('/indices/{index}/details', [IndexController::class, 'details'])->where('index', '[A-Za-z0-9_.-]+')->name('indices.details');
        Route::get('/shards', [ShardController::class, 'index'])->name('shards');
        Route::get('/shards/data', [ShardController::class, 'data'])->name('shards.data');
        Route::get('/shards/allocation-explain', [ShardController::class, 'allocationExplain'])->name('shards.allocation-explain');
        Route::get('/capacity', [CapacityController::class, 'index'])->name('capacity');
        Route::get('/capacity/data', [CapacityController::class, 'data'])->name('capacity.data');
        Route::get('/api-requests', [ApiRequestHistoryController::class, 'index'])->name('api-requests');
        Route::get('/api-requests/data', [ApiRequestHistoryController::class, 'data'])->name('api-requests.data');
        Route::get('/query-activity', [QueryActivityController::class, 'index'])->name('query-activity');
        Route::get('/query-activity/data', [QueryActivityController::class, 'data'])->name('query-activity.data');
        Route::get('/nodes', [NodeController::class, 'index'])->name('nodes');
        Route::get('/nodes/data', [NodeController::class, 'data'])->name('nodes.data');
        Route::get('/thread-pools', [ThreadPoolController::class, 'index'])->name('thread-pools');
        Route::get('/thread-pools/data', [ThreadPoolController::class, 'data'])->name('thread-pools.data');
        Route::get('/snapshots', [SnapshotController::class, 'index'])->name('snapshots');
        Route::get('/snapshots/data', [SnapshotController::class, 'data'])->name('snapshots.data');
        Route::get('/alerts', [AlertHistoryController::class, 'index'])->name('alerts');
        Route::get('/alerts/data', [AlertHistoryController::class, 'data'])->name('alerts.data');
        Route::get('/alerts/notifications', [AlertHistoryController::class, 'notifications'])->name('alerts.notifications');
        Route::get('/alerts/notifications/data', [AlertHistoryController::class, 'notificationData'])->name('alerts.notifications.data');
    });
