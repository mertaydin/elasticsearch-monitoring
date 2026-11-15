<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Facades\ElasticsearchMonitoring;
use ElasticsearchMonitoring\Http\Middleware\AuthorizeDashboard;
use ElasticsearchMonitoring\Services\DashboardAuthorizer;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardAuthorizationTest extends TestCase
{
    public function test_dashboard_authorization_callback_can_allow_access(): void
    {
        ElasticsearchMonitoring::auth(fn (Request $request): bool => $request->user()?->hasRole('SuperAdmin') ?? false);

        $request = Request::create('/monitoring/elasticsearch');
        $request->setUserResolver(fn () => new class
        {
            public function hasRole(string $role): bool
            {
                return $role === 'SuperAdmin';
            }
        });

        $response = app(AuthorizeDashboard::class)->handle($request, fn () => response('allowed'));

        $this->assertSame('allowed', $response->getContent());
    }

    public function test_dashboard_authorization_callback_can_deny_access(): void
    {
        ElasticsearchMonitoring::auth(fn (): bool => false);

        $this->expectException(HttpException::class);
        app(AuthorizeDashboard::class)->handle(
            Request::create('/monitoring/elasticsearch'),
            fn () => response('allowed')
        );
    }

    public function test_data_streams_page_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('elasticsearch-monitoring.data-streams'));
        $this->assertFalse(Route::has('elasticsearch-monitoring.data-streams.data'));
    }


    public function test_auto_refresh_labels_are_available_in_all_dashboard_locales(): void
    {
        foreach (['tr', 'en', 'es', 'hi', 'ar', 'zh_CN'] as $locale) {
            foreach ([
                'refresh_interval', 'auto_refresh', 'refresh_paused', 'pause_refresh',
                'resume_refresh', 'last_refresh', 'refresh_15_seconds', 'refresh_30_seconds',
                'refresh_1_minute', 'refresh_5_minutes',
            ] as $key) {
                $this->assertNotSame(
                    $key,
                    __('elasticsearch-monitoring::messages.' . $key, [], $locale),
                    "Missing translation [{$key}] for locale [{$locale}]."
                );
            }
        }
    }

    public function test_dashboard_remains_compatible_when_no_callback_is_registered(): void
    {
        $authorizer = app(DashboardAuthorizer::class);

        $this->assertFalse($authorizer->isConfigured());
        $response = app(AuthorizeDashboard::class)->handle(
            Request::create('/monitoring/elasticsearch'),
            fn () => response('allowed')
        );

        $this->assertSame('allowed', $response->getContent());
    }
}
