<?php

namespace Tests\Feature;

use App\Models\PageView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrafficTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_page_view_is_recorded_without_consent(): void
    {
        $this->get('/about')->assertOk();

        $this->assertSame(0, PageView::count());
    }

    public function test_page_view_is_recorded_after_consent(): void
    {
        $this->withUnencryptedCookie('cookie_consent', 'accepted')
            ->get('/about')
            ->assertOk();

        $this->assertSame(1, PageView::count());
        $this->assertSame('/about', PageView::first()->url);
    }

    public function test_declined_consent_records_nothing(): void
    {
        $this->withUnencryptedCookie('cookie_consent', 'declined')
            ->get('/about')
            ->assertOk();

        $this->assertSame(0, PageView::count());
    }

    public function test_admin_and_asset_paths_are_never_tracked(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withUnencryptedCookie('cookie_consent', 'accepted')
            ->get('/admin/statistics')
            ->assertOk();

        $this->assertSame(0, PageView::count());
    }

    public function test_consent_endpoint_sets_cookie(): void
    {
        $response = $this->post('/cookie-consent', ['consent' => 'accepted']);

        $response->assertRedirect();
        // The consent cookie is intentionally left unencrypted (bootstrap/app.php).
        $response->assertPlainCookie('cookie_consent', 'accepted');
    }

    public function test_consent_endpoint_rejects_invalid_value(): void
    {
        $this->post('/cookie-consent', ['consent' => 'maybe'])
            ->assertSessionHasErrors('consent');
    }

    public function test_admin_statistics_page_loads(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Seed a couple of views so aggregates run against real rows.
        PageView::create(['session_id' => 'abc123def', 'url' => '/gallery', 'country' => 'Germany', 'country_code' => 'DE']);
        PageView::create(['session_id' => 'abc123def', 'url' => '/shop', 'country' => 'Germany', 'country_code' => 'DE']);

        $this->actingAs($admin)
            ->get('/admin/statistics')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Statistics/Index')
                ->where('totals.totalViews', 2)
                ->where('totals.uniqueVisitors', 1)
                ->has('topCountries', 1)
                ->has('topPages', 2)
                ->has('recentVisitors', 1)
            );
    }

    public function test_statistics_page_requires_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/statistics')
            ->assertForbidden();
    }
}
