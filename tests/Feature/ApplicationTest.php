<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_user_can_view_applications_index(): void
    {
        $app = $this->user->applications()->create([
            'company'      => 'Initech Corp',
            'position'     => 'Software Engineer',
            'location'     => 'Austin, TX',
            'job_type'     => 'Full-time',
            'salary'       => 90000,
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->get(route('applications.index'));

        $response->assertStatus(200);
        $response->assertSee('Initech Corp');
        $response->assertSee('Software Engineer');
    }

    public function test_user_can_create_an_application(): void
    {
        $payload = [
            'company'        => 'Stark Industries',
            'position'       => 'Senior PHP Architect',
            'location'       => 'New York, NY',
            'job_type'       => 'Full-time',
            'salary'         => 120000,
            'applied_date'   => '2026-09-15',
            'follow_up_date' => '2026-09-22',
            'status'         => 'Interview',
            'job_url'        => 'https://example.com/jobs/123',
            'notes'          => 'First round interview with engineering manager.',
        ];

        $response = $this->actingAs($this->user)->post(route('applications.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('applications', [
            'user_id'  => $this->user->id,
            'company'  => 'Stark Industries',
            'position' => 'Senior PHP Architect',
            'status'   => 'Interview',
        ]);
    }

    public function test_create_application_validation_rules(): void
    {
        $response = $this->actingAs($this->user)->post(route('applications.store'), [
            'company'        => '',
            'position'       => '',
            'location'       => '',
            'job_type'       => 'InvalidType',
            'salary'         => 'not-a-number',
            'applied_date'   => 'not-a-date',
            'status'         => 'InvalidStatus',
            'job_url'        => 'not-a-valid-url',
        ]);

        $response->assertSessionHasErrors([
            'company',
            'position',
            'location',
            'job_type',
            'salary',
            'applied_date',
            'status',
            'job_url',
        ]);
    }

    public function test_user_can_view_their_own_application(): void
    {
        $app = $this->user->applications()->create([
            'company'      => 'Wayne Enterprises',
            'position'     => 'Laravel Engineer',
            'location'     => 'Gotham',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
            'notes'        => 'Confidential project discussion.',
        ]);

        $response = $this->actingAs($this->user)->get(route('applications.show', $app));

        $response->assertStatus(200);
        $response->assertSee('Wayne Enterprises');
        $response->assertSee('Confidential project discussion.');
    }

    public function test_user_can_update_their_own_application(): void
    {
        $app = $this->user->applications()->create([
            'company'      => 'Cyberdyne',
            'position'     => 'AI Engineer',
            'location'     => 'Sunnyvale, CA',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->put(route('applications.update', $app), [
            'company'      => 'Cyberdyne Systems',
            'position'     => 'Lead AI Engineer',
            'location'     => 'Sunnyvale, CA',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Interview',
            'notes'        => 'Moved to interview stage!',
        ]);

        $response->assertRedirect(route('applications.show', $app));
        $this->assertDatabaseHas('applications', [
            'id'       => $app->id,
            'company'  => 'Cyberdyne Systems',
            'position' => 'Lead AI Engineer',
            'status'   => 'Interview',
        ]);
    }

    public function test_user_can_delete_their_own_application(): void
    {
        $app = $this->user->applications()->create([
            'company'      => 'Oscorp',
            'position'     => 'Research Developer',
            'location'     => 'New York, NY',
            'job_type'     => 'Contract',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->delete(route('applications.destroy', $app));

        $response->assertRedirect(route('applications.index'));
        $this->assertDatabaseMissing('applications', ['id' => $app->id]);
    }

    public function test_search_and_filter_functionality(): void
    {
        $app1 = $this->user->applications()->create([
            'company'      => 'Alpha Solutions',
            'position'     => 'Backend Developer',
            'location'     => 'Remote',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Shortlisted',
        ]);

        $app2 = $this->user->applications()->create([
            'company'      => 'Beta Tech',
            'position'     => 'Frontend Developer',
            'location'     => 'Austin',
            'job_type'     => 'Part-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Rejected',
        ]);

        // Search by company
        $response = $this->actingAs($this->user)->get(route('applications.index', ['search' => 'Alpha']));
        $response->assertSee('Alpha Solutions');
        $response->assertDontSee('Beta Tech');

        // Search by position
        $response = $this->actingAs($this->user)->get(route('applications.index', ['search' => 'Frontend']));
        $response->assertSee('Beta Tech');
        $response->assertDontSee('Alpha Solutions');

        // Filter by status
        $response = $this->actingAs($this->user)->get(route('applications.index', ['status' => 'Shortlisted']));
        $response->assertSee('Alpha Solutions');
        $response->assertDontSee('Beta Tech');
    }

    public function test_dashboard_displays_stats_and_reminders(): void
    {
        $today = Carbon::today();

        // Application due today
        $this->user->applications()->create([
            'company'        => 'Due Today Corp',
            'position'       => 'Developer',
            'location'       => 'Remote',
            'job_type'       => 'Full-time',
            'applied_date'   => $today->copy()->subDays(5)->toDateString(),
            'follow_up_date' => $today->toDateString(),
            'status'         => 'Applied',
        ]);

        // Application selected (should not be in due follow-ups even if date is past)
        $this->user->applications()->create([
            'company'        => 'Selected Corp',
            'position'       => 'Engineer',
            'location'       => 'Remote',
            'job_type'       => 'Full-time',
            'applied_date'   => $today->copy()->subDays(10)->toDateString(),
            'follow_up_date' => $today->copy()->subDays(2)->toDateString(),
            'status'         => 'Selected',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Due Today Corp');
        $response->assertSee('Due Today');
        $response->assertDontSee('Follow up with Selected Corp');
    }

    // ==========================================
    // CRITICAL AUTHORIZATION / OWNERSHIP TESTS
    // ==========================================

    public function test_user_cannot_view_another_users_application(): void
    {
        $otherApp = $this->otherUser->applications()->create([
            'company'      => 'Private Corp',
            'position'     => 'Secret Agent',
            'location'     => 'Classified',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->get(route('applications.show', $otherApp));

        $response->assertStatus(403);
    }

    public function test_user_cannot_edit_another_users_application(): void
    {
        $otherApp = $this->otherUser->applications()->create([
            'company'      => 'Private Corp',
            'position'     => 'Secret Agent',
            'location'     => 'Classified',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->get(route('applications.edit', $otherApp));

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_another_users_application(): void
    {
        $otherApp = $this->otherUser->applications()->create([
            'company'      => 'Private Corp',
            'position'     => 'Secret Agent',
            'location'     => 'Classified',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->put(route('applications.update', $otherApp), [
            'company'      => 'Hacked Corp',
            'position'     => 'Hacked Position',
            'location'     => 'Nowhere',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('applications', ['company' => 'Hacked Corp']);
    }

    public function test_user_cannot_delete_another_users_application(): void
    {
        $otherApp = $this->otherUser->applications()->create([
            'company'      => 'Private Corp',
            'position'     => 'Secret Agent',
            'location'     => 'Classified',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->delete(route('applications.destroy', $otherApp));

        $response->assertStatus(403);
        $this->assertDatabaseHas('applications', ['id' => $otherApp->id]);
    }

    public function test_user_application_list_does_not_display_other_users_applications(): void
    {
        $otherApp = $this->otherUser->applications()->create([
            'company'      => 'Confidential Corp',
            'position'     => 'Hidden Role',
            'location'     => 'Remote',
            'job_type'     => 'Full-time',
            'applied_date' => now()->toDateString(),
            'status'       => 'Applied',
        ]);

        $response = $this->actingAs($this->user)->get(route('applications.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Confidential Corp');
        $response->assertDontSee('Hidden Role');
    }
}
