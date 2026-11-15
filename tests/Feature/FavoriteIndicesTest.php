<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Tests\TestCase;

class FavoriteIndicesTest extends TestCase
{
    public function test_index_management_page_renders_favorite_controls(): void
    {
        $this->withoutMiddleware()
            ->get('/monitoring/elasticsearch/indices')
            ->assertOk()
            ->assertSee('elasticsearch-monitoring:favorites')
            ->assertSee('favorites-only')
            ->assertSee('Add to favorites', false);
    }

    public function test_dashboard_renders_favorite_quick_access_section(): void
    {
        $this->withoutMiddleware()
            ->get('/monitoring/elasticsearch')
            ->assertOk()
            ->assertSee('favorite-indices-list')
            ->assertSee('elasticsearch-monitoring:favorites')
            ->assertSee('Favorite indices');
    }
}
