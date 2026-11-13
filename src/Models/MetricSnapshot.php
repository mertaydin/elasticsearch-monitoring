<?php

namespace ElasticsearchMonitoring\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property CarbonImmutable $collected_at
 * @property string $elasticsearch_version
 * @property array<string, mixed> $metrics
 */
class MetricSnapshot extends Model
{
    protected $table = 'elasticsearch_metric_snapshots';

    protected $fillable = ['collected_at', 'elasticsearch_version', 'metrics'];

    protected function casts(): array
    {
        return [
            'collected_at' => 'immutable_datetime',
            'metrics' => 'array',
        ];
    }
}