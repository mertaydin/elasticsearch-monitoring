<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\DashboardNoteController;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class DashboardNoteTestUser
{
    public function __construct(private string $id) {}

    public function getAuthIdentifier(): string
    {
        return $this->id;
    }
}

class DashboardNoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::dropIfExists('elasticsearch_dashboard_notes');
        Schema::create('elasticsearch_dashboard_notes', function (Blueprint $table): void {
            $table->id();
            $table->string('user_id', 191)->nullable()->index();
            $table->string('chart_key', 64)->index();
            $table->timestamp('noted_at')->index();
            $table->text('note');
            $table->timestamps();
            $table->index(['user_id', 'noted_at']);
        });
    }

    public function test_user_can_create_and_list_a_dashboard_note(): void
    {
        $created = app(DashboardNoteController::class)->store($this->request('POST', [
            'chart_key' => 'cpu-chart', 'noted_at' => '2026-11-15T14:30:00+03:00', 'note' => 'CPU increased during bulk import.',
        ]));
        $this->assertSame(201, $created->getStatusCode());
        $this->assertSame('cpu-chart', $created->getData(true)['note']['chart_key']);
        $this->assertDatabaseHas('elasticsearch_dashboard_notes', ['user_id' => 'user-1', 'note' => 'CPU increased during bulk import.']);
        $listed = app(DashboardNoteController::class)->index($this->request('GET'));
        $this->assertCount(1, $listed->getData(true)['notes']);
    }

    public function test_notes_are_isolated_between_users_and_can_be_updated_and_deleted(): void
    {
        $created = app(DashboardNoteController::class)->store($this->request('POST', [
            'chart_key' => 'cpu-chart', 'noted_at' => '2026-11-15T14:30:00+03:00', 'note' => 'Initial note',
        ]));
        $id = $created->getData(true)['note']['id'];
        $otherUser = app(DashboardNoteController::class)->index($this->request('GET', [], 'user-2'));
        $this->assertCount(0, $otherUser->getData(true)['notes']);
        $updated = app(DashboardNoteController::class)->update($this->request('PATCH', [
            'chart_key' => 'disk-chart', 'noted_at' => '2026-11-15T15:00:00+03:00', 'note' => 'Disk usage reviewed.',
        ]), $id);
        $this->assertSame('Disk usage reviewed.', $updated->getData(true)['note']['note']);
        $deleted = app(DashboardNoteController::class)->destroy($this->request('DELETE'), $id);
        $this->assertTrue($deleted->getData(true)['deleted']);
        $this->assertDatabaseMissing('elasticsearch_dashboard_notes', ['id' => $id]);
    }

    public function test_note_chart_key_is_validated(): void
    {
        $this->expectException(ValidationException::class);
        app(DashboardNoteController::class)->store($this->request('POST', [
            'chart_key' => 'unknown-chart', 'noted_at' => '2026-11-15T14:30:00+03:00', 'note' => 'Invalid chart',
        ]));
    }

    private function request(string $method, array $data = [], string $userId = 'user-1'): Request
    {
        $request = Request::create('/monitoring/elasticsearch/notes', $method, $data);
        $request->setUserResolver(fn () => new DashboardNoteTestUser($userId));
        return $request;
    }
}
