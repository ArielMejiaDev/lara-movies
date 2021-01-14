<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_authenticated_user_can_logout()
    {
        $this->withoutExceptionHandling();

        Passport::actingAs(
            User::factory()->guest()->create()
        );

        $response = $this->get(route('logout'));

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'You are successfully logged out',
        ]);
    }
}
