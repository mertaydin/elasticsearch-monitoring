<?php

namespace ElasticsearchMonitoring\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $alert_event_id
 * @property string $alert_code
 * @property string $channel
 * @property string $status
 * @property int $attempts
 * @property CarbonImmutable|null $sent_at
 * @property string|null $error
 * @property mixed $response
 */
class AlertNotificationDelivery extends Model
{
    protected $table = 'elasticsearch_alert_notification_deliveries';

    protected $fillable = [
        'alert_event_id',
        'alert_code',
        'channel',
        'status',
        'attempts',
        'sent_at',
        'error',
        'response',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'immutable_datetime',
            'response' => 'array',
        ];
    }

    public function alertEvent(): BelongsTo
    {
        return $this->belongsTo(AlertEvent::class, 'alert_event_id');
    }
}
