<?php

namespace Tests\Feature;

use App\Enums\LeadSource;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LeadStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_is_stored_and_returns_thanks_redirect(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        config([
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.chat_id' => '12345',
        ]);

        $response = $this->postJson(route('leads.store'), [
            'source' => LeadSource::HeaderCallback->value,
            'name' => 'Иван',
            'phone' => '+375291234567',
            'comment' => 'Тест',
            'consent' => true,
            'quiz_answers' => [
                'device' => 'laptop',
                'device_label' => 'Ноутбук',
                'problems' => ['Синий экран'],
            ],
            'utm_source' => 'google',
            'gclid' => 'abc123',
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('redirect', route('thanks'));

        $this->assertDatabaseHas('leads', [
            'source' => LeadSource::HeaderCallback->value,
            'name' => 'Иван',
            'phone' => '+375291234567',
            'utm_source' => 'google',
            'gclid' => 'abc123',
        ]);

        $lead = Lead::query()->first();
        $this->assertSame('laptop', $lead->quiz_answers['device']);
    }

    public function test_lead_requires_consent(): void
    {
        $response = $this->postJson(route('leads.store'), [
            'source' => LeadSource::FooterForm->value,
            'name' => 'Иван',
            'phone' => '+375291234567',
            'consent' => false,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('leads', 0);
    }
}
