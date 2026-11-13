<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Facades\ElasticsearchMonitoring;
use ElasticsearchMonitoring\Http\Middleware\AuthorizeDashboard;
use ElasticsearchMonitoring\Services\DashboardAuthorizer;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Http\Request;
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
