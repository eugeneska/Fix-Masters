<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Services\AnalyticsConversionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetrikaConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrika_uses_oauth_offline_upload_api(): void
    {
        config([
            'analytics.yandex_metrika_id' => '12345',
            'analytics.yandex_metrika_oauth_token' => 'test-oauth-token',
            'analytics.yandex_metrika_goal' => 'quality_lead',
        ]);

        Http::fake([
            'api-metrika.yandex.net/*' => Http::response([
                'uploading' => ['id' => 1, 'status' => 'UPLOADED'],
            ], 200),
        ]);

        $lead = Lead::factory()->create([
            'quality_status' => 'yes',
            'ym_client_id' => '1234567890.1234567890',
        ]);

        $service = app(AnalyticsConversionService::class);
        $result = $service->send($lead, 'metrika');

        $this->assertTrue($result['metrika']);
        $this->assertSame([], $result['errors']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/offline_conversions/upload')
                && $request->hasHeader('Authorization', 'OAuth test-oauth-token');
        });
    }

    public function test_admin_metrika_endpoint_requires_oauth_config(): void
    {
        config([
            'analytics.yandex_metrika_id' => '',
            'analytics.yandex_metrika_oauth_token' => '',
        ]);

        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'quality_status' => 'yes',
            'ym_client_id' => '123',
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.leads.conversion', $lead), [
            'target' => 'metrika',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Яндекс Метрика не настроена (YANDEX_METRIKA_ID, YANDEX_METRIKA_OAUTH_TOKEN).',
        ]);
    }
}
