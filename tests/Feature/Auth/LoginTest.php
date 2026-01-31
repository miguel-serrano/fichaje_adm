<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(): User
    {
        $roleId = \DB::table('roles')->insertGetId([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
            'is_super_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::factory()->create([
            'email' => 'test@example.com',
            'role_id' => $roleId,
        ]);
    }

    public function test_login_page_is_displayed(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_login_successful_redirects_to_backoffice(): void
    {
        $this->createUserWithRole();

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/backoffice');
        $this->assertAuthenticated();
    }

    public function test_login_with_wrong_password_fails(): void
    {
        $this->createUserWithRole();

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_invalid_email_fails(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_redirects_to_login(): void
    {
        $user = $this->createUserWithRole();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/backoffice');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_backoffice(): void
    {
        $user = $this->createUserWithRole();
        $this->actingAs($user);

        $response = $this->get('/backoffice');

        $response->assertStatus(200);
    }
}
