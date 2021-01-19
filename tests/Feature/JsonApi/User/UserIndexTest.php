<?php

namespace Tests\Feature\JsonApi\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_see_users_index()
    {
        User::factory()->times(15)->create();

        $route = route('api:v1:users.index');

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_see_users_index()
    {
        Passport::actingAs(User::factory()->guest()->create());

        User::factory()->times(15)->create();

        $route = route('api:v1:users.index');

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_users_index_behaves_as_expected()
    {
        /** @var User $users */
        $users = User::factory()->times(15)->create();

        Passport::actingAs(User::factory()->admin()->create());

        $route = route('api:v1:users.index');

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'data' => [
                [
                    'type' => 'users',
                    'id' => $users->first()->getRouteKey(),
                    'attributes' => [
                        'name' => $users->first()->name,
                        'email' => $users->first()->email,
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function it_tests_users_index_pagination_works()
    {
        /** @var User $users */
        User::factory()->times(14)->create();

        Passport::actingAs(User::factory()->admin()->create());

        $route = route('api:v1:users.index', [
            'page[number]' => 1,
            'page[size]' => 5,
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'links' => [
                'first' => route('api:v1:users.index', ['page[number]' => 1, 'page[size]' => 5]),
                'next' => route('api:v1:users.index', ['page[number]' => 2, 'page[size]' => 5]),
                'last' => route('api:v1:users.index', ['page[number]' => 3, 'page[size]' => 5]),
            ]
        ]);
    }
}
