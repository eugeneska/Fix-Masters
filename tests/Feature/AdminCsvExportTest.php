<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCsvExportTest extends TestCase
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

    public function test_csv_export_respects_period_filter(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create([
            'name' => 'Today Lead',
            'created_at' => Carbon::parse('2026-05-30 08:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'Yesterday Lead',
            'created_at' => Carbon::parse('2026-05-29 20:00:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.export.csv', ['period' => 'day']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Today Lead', $content);
        $this->assertStringNotContainsString('Yesterday Lead', $content);
    }

    public function test_csv_export_respects_custom_date_range_and_source(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create([
            'name' => 'In Range Google',
            'source' => 'google',
            'created_at' => Carbon::parse('2026-05-15 10:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'Out Of Range Google',
            'source' => 'google',
            'created_at' => Carbon::parse('2026-05-01 10:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'In Range Yandex',
            'source' => 'yandex',
            'created_at' => Carbon::parse('2026-05-15 11:00:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.export.csv', [
            'period' => 'custom',
            'date_from' => '2026-05-10',
            'date_to' => '2026-05-20',
            'source' => 'google',
        ]));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('In Range Google', $content);
        $this->assertStringNotContainsString('Out Of Range Google', $content);
        $this->assertStringNotContainsString('In Range Yandex', $content);
    }

    public function test_csv_export_formats_date_for_excel(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create([
            'name' => 'Date Format Lead',
            'created_at' => Carbon::parse('2026-05-30 14:30:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.export.csv'));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString("\t30.05.2026 14:30:00", $content);
        $this->assertStringNotContainsString('2026-05-30', $content);
    }

    public function test_csv_export_orders_leads_like_admin_list(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create([
            'name' => 'Newer Lead',
            'created_at' => Carbon::parse('2026-05-28 10:00:00', 'Europe/Minsk'),
        ]);
        Lead::factory()->create([
            'name' => 'Older Lead',
            'created_at' => Carbon::parse('2026-05-10 10:00:00', 'Europe/Minsk'),
        ]);

        $response = $this->actingAs($user)->get(route('admin.export.csv', [
            'sort' => 'created_at',
            'direction' => 'desc',
        ]));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertLessThan(
            strpos($content, 'Older Lead'),
            strpos($content, 'Newer Lead'),
        );
    }

    public function test_csv_export_respects_qualification_filter(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create([
            'name' => 'Qualified Lead',
            'qualification_status' => Lead::STATUS_YES,
        ]);
        Lead::factory()->create([
            'name' => 'Not Qualified Lead',
            'qualification_status' => Lead::STATUS_NO,
        ]);

        $response = $this->actingAs($user)->get(route('admin.export.csv', [
            'qualification' => 'yes',
        ]));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Qualified Lead', $content);
        $this->assertStringNotContainsString('Not Qualified Lead', $content);
    }
}
