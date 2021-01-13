<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_login_should_throw_error_with_invalid_request_method()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(405);
    }

    /** @test */
    public function test_login_should_throw_error_with_a_user_that_does_not_exists()
    {
        $response = $this->postJson(route('login'), [
            'email' => 'john@doe.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function test_login_should_throw_error_with_a__wrong_user_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'somerandomtext',
        ]);


        $response->assertJson([
            'message' => 'Invalid user/password',
        ], 401);
    }
}
