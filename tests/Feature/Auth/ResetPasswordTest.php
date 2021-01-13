<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_reset_password_feature_throws_validation_errors_with_invalid_data()
    {
        $resetRequest = $this->postJson(route('reset-password'), [
            'token' => '',
            'password' => '',
            'password_confirm' => '',
        ]);

        $resetRequest->assertJsonValidationErrors(['token', 'password']);
    }

    /** @test */
    public function test_reset_password_feature_throws_error_404_with_not_existing_token()
    {
        $resetRequest = $this->postJson(route('reset-password'), [
            'token' => 'somerandomstring',
            'password' => 'password',
            'password_confirm' => 'password',
        ]);

        $resetRequest->assertJson([
           'message' => 'invalid token',
        ]);

        $resetRequest->assertStatus(400);
    }

    /** @test */
    public function test_to_generate_a_token_and_send_a_reset_password_request_with_that_token()
    {
        $user = User::factory()->create();

        Notification::fake();

        $response = $this->postJson(route('forgot-password'), [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            ForgotPasswordNotification::class,
            function ($notification) {

                $resetRequest = $this->postJson(route('reset-password'), [
                    'token' => $notification->token,
                    'password' => 'password',
                    'password_confirm' => 'password',
                ]);

                $resetRequest->assertJson([
                    'message' => 'password updated',
                ]);

                return $notification;
            }
        );

        $response->assertJsonMissingValidationErrors('email');

        $response->assertJson([
            'message' => 'check your email',
        ]);
    }
}
