<?php

namespace Tests\Feature\JsonApi\Purchase;

use App\Models\Movie;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PurchaseCreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_create_a_purchase()
    {
        $movie = Movie::factory()->create();

        $route = route('api:v1:purchases.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'purchases',
                'attributes' => [
                    'movie_id' => $movie->getRouteKey(),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_cannot_create_a_purchase_without_a_movie_id()
    {
        Passport::actingAs(User::factory()->create());

        $route = route('api:v1:purchases.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'purchases',
                'attributes' => [
                    'movie_id' => '',
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee(['pointer' => '\/data\/attributes\/movie_id'])
            ->assertSee(['detail' => 'The movie id field is required']);
    }

    /** @test */
    public function it_tests_cannot_create_a_purchase_without_a_existing_movie_id()
    {
        Passport::actingAs(User::factory()->create());

        $route = route('api:v1:purchases.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'purchases',
                'attributes' => [
                    'movie_id' => 1, // there is no movie created
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee(['pointer' => '\/data\/attributes\/movie_id'])
            ->assertSee(['detail' => 'The selected movie id is invalid.']);
    }

    /** @test */
    public function it_tests_cannot_create_a_purchase_without_movie_stock()
    {
        Passport::actingAs(User::factory()->create());

        $movie = Movie::factory()->create([
            'stock' => 0,
        ]);

        $route = route('api:v1:purchases.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'purchases',
                'attributes' => [
                    'movie_id' => $movie->getRouteKey(), // there is no movie created
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee(['pointer' => '\/data\/attributes\/movie_id'])
            ->assertSee(['detail' => 'There is no stock for this movie.']);
    }

    /** @test */
    public function it_tests_create_a_purchase_behaves_as_expected()
    {
        Passport::actingAs($user = User::factory()->create());

        $movie = Movie::factory()->create();

        $route = route('api:v1:purchases.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'purchases',
                'attributes' => [
                    'movie_id' => $movie->getRouteKey(),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'data' => [
                'id' => 1,
                'attributes' => [
                    'user_id' => $user->getRouteKey(),
                    'movie_id' => $movie->getRouteKey(),
                ]
            ]
        ]);

    }
}
