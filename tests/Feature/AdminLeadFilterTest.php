<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLeadFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-05-30 12:00:00', 'Europe/Minsk'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_filters_by_today(): void
    {
        $user = User::factory()->create();
        $todayLead = Lead::factory()->create([
            'name' => 'Today Lead',
            'created_at' => Carbon::parse('2026-05-30 08:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'Yesterday Lead',
            'created_at' => Carbon::parse('2026-05-29 20:00:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.leads.index', ['period' => 'day']));

        $response->assertOk();
        $response->assertSee($todayLead->name);
        $response->assertDontSee('Yesterday Lead');
    }

    public function test_filters_by_specific_date(): void
    {
        $user = User::factory()->create();
        $targetLead = Lead::factory()->create([
            'name' => 'May 28 Lead',
            'created_at' => Carbon::parse('2026-05-28 10:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'May 29 Lead',
            'created_at' => Carbon::parse('2026-05-29 10:00:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.leads.index', [
            'period' => 'date',
            'date_from' => '2026-05-28',
        ]));

        $response->assertOk();
        $response->assertSee($targetLead->name);
        $response->assertDontSee('May 29 Lead');
    }

    public function test_filters_by_custom_date_range(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create([
            'name' => 'May 01 Lead',
            'created_at' => Carbon::parse('2026-05-01 10:00:00', 'Europe/Minsk'),
        ]);
        $targetLead = Lead::factory()->create([
            'name' => 'May 15 Lead',
            'created_at' => Carbon::parse('2026-05-15 10:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'May 29 Lead',
            'created_at' => Carbon::parse('2026-05-29 10:00:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.leads.index', [
            'period' => 'custom',
            'date_from' => '2026-05-10',
            'date_to' => '2026-05-20',
        ]));

        $response->assertOk();
        $response->assertSee($targetLead->name);
        $response->assertDontSee('May 01 Lead');
        $response->assertDontSee('May 29 Lead');
    }

    public function test_applies_date_range_when_period_is_all_but_dates_are_set(): void
    {
        $user = User::factory()->create();
        $targetLead = Lead::factory()->create([
            'name' => 'May 28 Lead',
            'created_at' => Carbon::parse('2026-05-28 10:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'May 29 Lead',
            'created_at' => Carbon::parse('2026-05-29 10:00:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.leads.index', [
            'period' => 'all',
            'date_from' => '2026-05-28',
            'date_to' => '2026-05-28',
        ]));

        $response->assertOk();
        $response->assertSee($targetLead->name);
        $response->assertDontSee('May 29 Lead');
    }
}
