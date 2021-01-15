<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_register_should_throw_error_with_invalid_request_method()
    {
        $response = $this->getJson(route('register'));

        $response->assertStatus(405);
    }

    /** @test */
    public function test_register_should_throw_error_with_invalid_data()
    {
        $response = $this->postJson(route('register'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirm' => '',
        ]);

        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function test_register_should_works_with_valid_data()
    {

        $data = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'password',
            'password_confirm' => 'password',
        ];

        $response = $this->postJson(route('register'), $data);

        $response->assertJsonMissingValidationErrors(['name', 'email', 'password']);

        $user = User::first();

        $response->assertJsonFragment([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }
}
