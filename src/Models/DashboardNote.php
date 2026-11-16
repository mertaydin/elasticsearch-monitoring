<?php

namespace ElasticsearchMonitoring\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $user_id
 * @property string $chart_key
 * @property \Illuminate\Support\Carbon $noted_at
 * @property string $note
 */
class DashboardNote extends Model
{
    protected $table = 'elasticsearch_dashboard_notes';

    protected $fillable = ['user_id', 'chart_key', 'noted_at', 'note'];

    protected $hidden = ['user_id'];

    protected function casts(): array
    {
        return ['noted_at' => 'immutable_datetime'];
    }
}
