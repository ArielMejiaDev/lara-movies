<?php

namespace Tests\Feature\JsonApi\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_unauthenticated_request_cannot_see_user_show()
    {
        $user = User::factory()->create();

        $route = route('api:v1:users.read', $user);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_unauthorized_request_cannot_see_user_show()
    {
        $user = User::factory()->create();

        Passport::actingAs(User::factory()->guest()->create());

        $route = route('api:v1:users.read', $user);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_user_show_behaves_as_expected()
    {
        /** @var User $user */
        $user = User::factory()->create();

        Passport::actingAs(User::factory()->admin()->create());

        $route = route('api:v1:users.read', $user);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'type' => 'users',
                'id' => $user->getRouteKey(),
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->name,
                ]
            ]
        ]);
    }
}
