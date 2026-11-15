<?php
namespace ElasticsearchMonitoring\Models;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
/**
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property int $id
 * @property string $method
 * @property string $endpoint
 * @property int|null $status_code
 * @property float $duration_ms
 * @property bool $successful
 * @property string|null $error
 */
class ApiRequestLog extends Model
{
    protected $table = 'elasticsearch_api_request_logs';
    protected $fillable = ['method', 'endpoint', 'status_code', 'duration_ms', 'successful', 'error'];
    protected function casts(): array
    {
        return ['status_code' => 'integer', 'duration_ms' => 'float', 'successful' => 'boolean'];
    }
}
