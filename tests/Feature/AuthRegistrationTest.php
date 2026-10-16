<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load_plain_css_and_inline_registration_javascript(): void
    {
        foreach (['/', '/login', '/register'] as $path) {
            $response = $this->get($path);

            $response->assertOk();
            $response->assertSee(asset('css/app.css'));
        }

        $this->get('/register')
            ->assertSee('name="domisili"', false)
            ->assertSee('name="sub_domisili"', false)
            ->assertSee('name="kecamatan"', false)
            ->assertSee('name="wilayah_akhir"', false);
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('name="domisili"', false);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 1',
            'domisili' => 'Bogor',
            'sub_domisili' => 'kabupaten',
            'kecamatan' => 'Cibinong',
            'wilayah_akhir' => 'Cirimekar',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertSame('Cibinong', $user->kecamatan);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_registration_shows_validation_errors_and_keeps_old_input(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'address' => 'Jl. Contoh No. 1',
            'domisili' => 'Bogor',
            'sub_domisili' => 'kabupaten',
            'kecamatan' => 'Cibinong',
            'wilayah_akhir' => 'Cirimekar',
            'password' => 'password123',
            'password_confirmation' => 'password321',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email', 'password']);
        $response->assertSessionHas('_old_input.email', 'not-an-email');
        $this->assertDatabaseCount('users', 0);
    }
}
