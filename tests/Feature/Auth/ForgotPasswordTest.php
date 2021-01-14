<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_forgot_password_feature_throws_an_error_with_invalid_email()
    {
        Notification::fake();

        $response = $this->postJson(route('forgot-password'), [
            'email' => 'not@registeremail.com',
        ]);

        $response->assertStatus(404);

        Notification::assertNothingSent();
    }

    /** @test */
    public function test_forgot_password_feature_throws_an_error_with_invalid_data()
    {
        Notification::fake();

        $response = $this->postJson(route('forgot-password'), [
            'email' => 'notregisteremail',
        ]);

        $response->assertJsonValidationErrors('email');

        Notification::assertNothingSent();
    }

    /** @test */
    public function test_forgot_password_feature_works_with_a_valid_an_existing_user_email()
    {
        $user = User::factory()->create();

        Notification::fake();

        $response = $this->postJson(route('forgot-password'), [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            ForgotPasswordNotification::class,
            function ($notification, $channels, $notifiable) use ($user) {
                return $notifiable->routes['mail'] === $user->email;
            }
        );

        $response->assertJsonMissingValidationErrors('email');

        $response->assertJson([
            'message' => 'check your email',
        ]);
    }
}
