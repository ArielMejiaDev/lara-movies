<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class VerifiedEmailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Route::any('/test-route', ['as' => 'test-route'])->middleware('auth:api', 'verified');
    }

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_access_to_a_route_with_verified_middleware()
    {
        $route = route('test-route');

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_a_user_without_email_verified_cannot_access_to_a_route_with_verified_middleware()
    {
        Passport::actingAs(User::factory()->create([
            'email_verified_at' => null,
        ]));

        $route = route('verified-only');

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertSee(['detail' => 'Your email address is not verified.']);
    }

    /** @test */
    public function it_tests_a_user_with_email_verified_can_access_to_routes_protected_with_middleware_verified()
    {
        /** @var User $user */
        Passport::actingAs($user = User::factory()->create([
            'email_verified_at' => now(),
        ]));

        $route = route('verified-only');

        $response = $this->jsonApi()->get($route);

        $response->assertSee(['message' => $user->name . ' you are verified!']);
    }

    /** @test */
    public function it_tests_email_resend()
    {
        Passport::actingAs(User::factory()->create([
            'email_verified_at' => null,
        ]));

       $route = route('verification.resend');

       $response = $this->jsonApi()->get($route);

       $response->assertSee(['message' => 'Email Sent']);
    }
}
