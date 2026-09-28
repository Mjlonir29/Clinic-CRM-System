<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_url_redirects_guest_to_login_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Practice Sign In');
    }

    public function test_authenticated_user_accessing_root_redirects_to_dashboard(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/dashboard');
    }
}
