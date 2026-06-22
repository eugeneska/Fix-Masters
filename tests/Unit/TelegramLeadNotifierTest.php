<?php

namespace Tests\Unit;

use App\Models\Lead;
use App\Services\TelegramLeadNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramLeadNotifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_telegram_message_includes_utm_and_order_details(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        config([
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.chat_id' => '12345',
        ]);

        $lead = Lead::factory()->create([
            'source' => 'quiz_contact',
            'name' => 'Иван',
            'phone' => '+375291234567',
            'comment' => 'Нужен срочный выезд',
            'ip' => '203.0.113.45',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'repair_minsk',
            'gclid' => 'abc123',
            'quiz_answers' => [
                'device_label' => 'Ноутбук',
                'problems' => ['Не включается'],
                'brand' => 'Apple',
            ],
        ]);

        $notifier = app(TelegramLeadNotifier::class);
        $notifier->send($lead);

        Http::assertSent(function ($request) {
            $text = $request->data()['text'] ?? '';

            return str_contains($text, 'Заказ услуги:')
                && str_contains($text, 'Ноутбук')
                && str_contains($text, 'Apple')
                && str_contains($text, 'UTM:')
                && str_contains($text, 'source=google')
                && str_contains($text, 'medium=cpc')
                && str_contains($text, 'gclid=abc123')
                && str_contains($text, '203.0.113.45')
                && str_contains($text, 'Нужен срочный выезд');
        });
    }
}
