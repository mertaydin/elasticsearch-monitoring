<?php

namespace ElasticsearchMonitoring\Models;

use Illuminate\Database\Eloquent\Model;

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