<?php

namespace Tests\Feature\JsonApi\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_update_a_user()
    {
        /** @var User $user */
        $user = User::factory()->guest()->create();

        $route = route('api:v1:users.update', $user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'users',
                'id' => $user->getRouteKey(),
                'attributes' => [
                    'user_id' => $user->getRouteKey(),
                ]
            ]
        ])->patch($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_update_a_user()
    {
        /** @var User $user */
        $user = User::factory()->guest()->create();

        Passport::actingAs(User::factory()->guest()->create());

        $route = route('api:v1:users.update', $user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'user_id' => $user->getRouteKey(),
                ]
            ]
        ])->patch($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_cannot_update_a_user_without_user_id()
    {
        /** @var User $user */
        $user = User::factory()->guest()->create();

        Passport::actingAs(User::factory()->admin()->create());

        $route = route('api:v1:users.update', $user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'user_id' => '',
                ]
            ]
        ])->patch($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee(['description' => 'The user id field is required.'])
            ->assertSee(['pointer' => '\/data\/attributes\/user_id'])
        ;
    }

    /** @test */
    public function it_tests_update_user_behaves_as_expected()
    {
        /** @var User $user */
        $user = User::factory()->guest()->create();

        Passport::actingAs(User::factory()->admin()->create());

        $route = route('api:v1:users.update', $user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'user_id' => $user->getRouteKey(),
                ]
            ]
        ])->patch($route);

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
