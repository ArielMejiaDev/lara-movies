<?php

namespace Tests\Feature\JsonApi\Purchase;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PurchaseShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_show_a_purchase()
    {
        $purchase = Purchase::factory()->create();

        $route = route('api:v1:purchases.read', $purchase);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_show_a_purchase()
    {
        Passport::actingAs($user = User::factory()->guest()->create());

        $purchase = Purchase::factory()->create();

        $route = route('api:v1:purchases.read', $purchase);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_show_a_purchase_has_relationships()
    {
        Passport::actingAs($user = User::factory()->admin()->create());

        $purchase = Purchase::factory()->create();

        $route = route('api:v1:purchases.read', $purchase);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJsonFragment([
            'relationships' => [
                'users' => [
                    'links' => [
                        'self' => route('api:v1:purchases.relationships.users.read', $purchase),
                        'related' => route('api:v1:purchases.relationships.users', $purchase)
                    ]
                ],
                'movies' => [
                    'links' => [
                        'self' => route('api:v1:purchases.relationships.movies.read', $purchase),
                        'related' => route('api:v1:purchases.relationships.movies', $purchase)
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function it_tests_show_a_purchase_has_includes_with_movies_and_users()
    {
        Passport::actingAs($user = User::factory()->admin()->create());

        /** @var Purchase $purchase */
        $purchase = Purchase::factory()->create();

        $route = route('api:v1:purchases.read', $purchase);

        $response = $this->jsonApi()->includePaths('movies,users')->get($route);

        $response->assertOk();

        $response->assertJson([
            'included' => [
                [
                    'type' => 'users',
                    'id' => $purchase->user_id,
                ],
                [
                    'type' => 'movies',
                    'id' => $purchase->movie_id,
                ],
            ]
        ]);
    }

    /** @test */
    public function it_tests_show_a_purchase_behaves_as_expected()
    {
        Passport::actingAs($user = User::factory()->admin()->create());

        $purchase = Purchase::factory()->create();

        $route = route('api:v1:purchases.read', $purchase);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertSee(['id' => $purchase->getRouteKey()]);
    }
}
