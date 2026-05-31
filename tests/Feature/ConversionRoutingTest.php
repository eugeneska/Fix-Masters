<?php

namespace Tests\Feature;

use App\Enums\LeadTrafficChannel;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConversionRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_gclid_is_routed_to_both_systems(): void
    {
        $lead = Lead::factory()->make(['gclid' => 'abc123']);

        $this->assertSame(LeadTrafficChannel::GoogleAds, $lead->trafficChannel());
        $this->assertSame('both', $lead->conversionTarget());
    }

    public function test_yandex_yclid_is_routed_to_metrika_only(): void
    {
        $lead = Lead::factory()->make(['yclid' => 'xyz789']);

        $this->assertSame(LeadTrafficChannel::YandexAds, $lead->trafficChannel());
        $this->assertSame('metrika', $lead->conversionTarget());
    }

    public function test_social_traffic_is_routed_to_both_systems(): void
    {
        $lead = Lead::factory()->make(['utm_source' => 'vk', 'utm_medium' => 'social']);

        $this->assertSame(LeadTrafficChannel::Social, $lead->trafficChannel());
        $this->assertSame('both', $lead->conversionTarget());
    }

    public function test_organic_traffic_is_routed_to_both_systems(): void
    {
        $lead = Lead::factory()->make();

        $this->assertSame(LeadTrafficChannel::Other, $lead->trafficChannel());
        $this->assertSame('both', $lead->conversionTarget());
    }

    public function test_google_organic_utm_is_not_classified_as_google_ads(): void
    {
        $lead = Lead::factory()->make([
            'utm_source' => 'google',
            'utm_medium' => 'organic',
        ]);

        $this->assertSame(LeadTrafficChannel::Other, $lead->trafficChannel());
        $this->assertSame('both', $lead->conversionTarget());
    }

    public function test_yandex_organic_utm_is_not_classified_as_yandex_ads(): void
    {
        $lead = Lead::factory()->make([
            'utm_source' => 'yandex',
            'utm_medium' => 'organic',
        ]);

        $this->assertSame(LeadTrafficChannel::Other, $lead->trafficChannel());
        $this->assertSame('both', $lead->conversionTarget());
    }

    public function test_google_ads_utm_with_cpc_medium_is_detected(): void
    {
        $lead = Lead::factory()->make([
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
        ]);

        $this->assertSame(LeadTrafficChannel::GoogleAds, $lead->trafficChannel());
    }

    public function test_yandex_ads_utm_with_cpc_medium_is_detected(): void
    {
        $lead = Lead::factory()->make([
            'utm_source' => 'yandex',
            'utm_medium' => 'cpc',
        ]);

        $this->assertSame(LeadTrafficChannel::YandexAds, $lead->trafficChannel());
    }

    public function test_google_click_id_has_priority_over_yandex_utm(): void
    {
        $lead = Lead::factory()->make([
            'gclid' => 'abc',
            'utm_source' => 'yandex',
            'utm_medium' => 'cpc',
        ]);

        $this->assertSame(LeadTrafficChannel::GoogleAds, $lead->trafficChannel());
    }

    public function test_quality_yes_auto_sends_to_both_for_google_lead(): void
    {
        $this->fakeAnalyticsApis();

        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'gclid' => 'google-click-id',
            'ga_client_id' => 'ga.1.2',
            'ym_client_id' => '1234567890.1234567890',
        ]);

        $response = $this->actingAs($user)->patchJson(route('admin.leads.update', $lead), [
            'quality_status' => 'yes',
        ]);

        $response->assertOk();
        $response->assertJsonPath('conversion.target', 'both');
        $response->assertJsonPath('conversion.ga4', true);
        $response->assertJsonPath('conversion.metrika', true);

        $lead->refresh();
        $this->assertTrue($lead->ga4_conversion_sent);
        $this->assertTrue($lead->metrika_conversion_sent);
    }

    public function test_quality_yes_auto_sends_only_to_metrika_for_yandex_lead(): void
    {
        $this->fakeAnalyticsApis();

        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'yclid' => 'yandex-click-id',
            'ga_client_id' => 'ga.1.2',
            'ym_client_id' => '1234567890.1234567890',
        ]);

        $response = $this->actingAs($user)->patchJson(route('admin.leads.update', $lead), [
            'quality_status' => 'yes',
        ]);

        $response->assertOk();
        $response->assertJsonPath('conversion.target', 'metrika');
        $response->assertJsonPath('conversion.ga4', false);
        $response->assertJsonPath('conversion.metrika', true);

        $lead->refresh();
        $this->assertFalse($lead->ga4_conversion_sent);
        $this->assertTrue($lead->metrika_conversion_sent);
    }

    public function test_quality_yes_auto_sends_to_both_for_social_lead(): void
    {
        $this->fakeAnalyticsApis();

        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'utm_source' => 'vk',
            'utm_medium' => 'social',
            'ga_client_id' => 'ga.1.2',
            'ym_client_id' => '1234567890.1234567890',
        ]);

        $response = $this->actingAs($user)->patchJson(route('admin.leads.update', $lead), [
            'quality_status' => 'yes',
        ]);

        $response->assertOk();
        $response->assertJsonPath('conversion.target', 'both');
        $response->assertJsonPath('conversion.ga4', true);
        $response->assertJsonPath('conversion.metrika', true);
    }

    public function test_quality_yes_auto_sends_to_both_for_organic_lead(): void
    {
        $this->fakeAnalyticsApis();

        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'ga_client_id' => 'ga.1.2',
            'ym_client_id' => '1234567890.1234567890',
        ]);

        $response = $this->actingAs($user)->patchJson(route('admin.leads.update', $lead), [
            'quality_status' => 'yes',
        ]);

        $response->assertOk();
        $response->assertJsonPath('conversion.target', 'both');
        $response->assertJsonPath('conversion.ga4', true);
        $response->assertJsonPath('conversion.metrika', true);
    }

    public function test_quality_yes_does_not_send_on_lead_creation(): void
    {
        Http::fake();

        Lead::factory()->create([
            'gclid' => 'google-click-id',
        ]);

        Http::assertNothingSent();
    }

    public function test_quality_yes_does_not_resend_when_already_sent(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'quality_status' => 'no',
            'gclid' => 'google-click-id',
            'ga4_conversion_sent' => true,
            'metrika_conversion_sent' => true,
        ]);

        $response = $this->actingAs($user)->patchJson(route('admin.leads.update', $lead), [
            'quality_status' => 'yes',
        ]);

        $response->assertOk();
        $response->assertJsonMissing(['conversion']);

        Http::assertNothingSent();
    }

    public function test_manual_conversion_respects_yandex_routing(): void
    {
        $this->fakeAnalyticsApis();

        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'quality_status' => 'yes',
            'yclid' => 'yandex-click-id',
            'ym_client_id' => '1234567890.1234567890',
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.leads.conversion', $lead), [
            'target' => 'both',
        ]);

        $response->assertOk();
        $response->assertJsonPath('conversion.target', 'metrika');
        $response->assertJsonPath('conversion.ga4', false);
        $response->assertJsonPath('conversion.metrika', true);

        Http::assertSentCount(1);
    }

    private function fakeAnalyticsApis(): void
    {
        config([
            'analytics.ga4_measurement_id' => 'G-TEST',
            'analytics.ga4_api_secret' => 'secret',
            'analytics.yandex_metrika_id' => '12345',
            'analytics.yandex_metrika_oauth_token' => 'test-oauth-token',
        ]);

        Http::fake([
            'www.google-analytics.com/*' => Http::response('', 204),
            'api-metrika.yandex.net/*' => Http::response([
                'uploading' => ['id' => 1, 'status' => 'UPLOADED'],
            ], 200),
        ]);
    }
}
