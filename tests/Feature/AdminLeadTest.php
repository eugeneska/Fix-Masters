<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_leads(): void
    {
        $response = $this->get(route('admin.leads.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_is_rate_limited_after_failed_attempts(): void
    {
        User::factory()->create(['email' => 'admin@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.submit'), [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrorsIn('default', 'email', function ($message) {
            return str_contains($message, 'Слишком много попыток');
        });
    }

    public function test_admin_can_view_leads_list(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.leads.index'));

        $response->assertOk();
        $response->assertSee('Список заявок');
    }

    public function test_admin_can_update_lead_statuses(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();

        $response = $this->actingAs($user)->patchJson(route('admin.leads.update', $lead), [
            'qualification_status' => 'yes',
            'quality_status' => 'no',
            'admin_note' => 'Тестовое примечание',
        ]);

        $response->assertOk();
        $lead->refresh();
        $this->assertSame('yes', $lead->qualification_status);
        $this->assertSame('no', $lead->quality_status);
        $this->assertSame('Тестовое примечание', $lead->admin_note);
    }

    public function test_admin_can_delete_lead(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.leads.destroy', $lead));

        $response->assertRedirect(route('admin.leads.index'));
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_conversion_blocked_when_already_sent_to_ga4(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'quality_status' => 'yes',
            'ga4_conversion_sent' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.leads.conversion', $lead), [
            'target' => 'ga4',
        ]);

        $response->assertStatus(422);
    }
}
