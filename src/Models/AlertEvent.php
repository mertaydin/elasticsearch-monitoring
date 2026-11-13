<?php

namespace ElasticsearchMonitoring\Models;

use Illuminate\Database\Eloquent\Model;

class AlertEvent extends Model
{
    protected $table = 'elasticsearch_alert_events';

    protected $fillable = ['code', 'severity', 'title', 'message', 'value', 'threshold', 'first_seen_at', 'last_seen_at', 'resolved_at', 'occurrences'];

    protected function casts(): array
    {
        return [
            'value' => 'json',
            'threshold' => 'json',
            'first_seen_at' => 'immutable_datetime',
            'last_seen_at' => 'immutable_datetime',
            'resolved_at' => 'immutable_datetime',
        ];
    }
}
