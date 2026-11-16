<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\DashboardNote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardNoteController
{
    private const CHART_KEYS = ['cpu-chart', 'storage-chart', 'disk-chart', 'search-chart', 'indexing-chart', 'segments-chart', 'documents-chart'];

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'notes' => $this->notesForUser($request)->latest('noted_at')->limit(200)->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chart_key' => ['required', 'string', 'in:'.implode(',', self::CHART_KEYS)],
            'noted_at' => ['required', 'date'],
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $note = DashboardNote::query()->create([...$validated, 'user_id' => $this->userId($request)]);

        return response()->json(['note' => $note], 201);
    }

    public function update(Request $request, int $note): JsonResponse
    {
        $validated = $request->validate([
            'chart_key' => ['required', 'string', 'in:'.implode(',', self::CHART_KEYS)],
            'noted_at' => ['required', 'date'],
            'note' => ['required', 'string', 'max:2000'],
        ]);
        $dashboardNote = $this->notesForUser($request)->findOrFail($note);
        $dashboardNote->update($validated);

        return response()->json(['note' => $dashboardNote->refresh()]);
    }

    public function destroy(Request $request, int $note): JsonResponse
    {
        $this->notesForUser($request)->findOrFail($note)->delete();

        return response()->json(['deleted' => true]);
    }

    /** @return Builder<DashboardNote> */
    private function notesForUser(Request $request): Builder
    {
        return DashboardNote::query()->where('user_id', $this->userId($request));
    }

    private function userId(Request $request): ?string
    {
        $identifier = $request->user()?->getAuthIdentifier();

        return $identifier === null ? null : (string) $identifier;
    }
}
