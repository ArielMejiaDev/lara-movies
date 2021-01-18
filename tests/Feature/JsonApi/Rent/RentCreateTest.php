<?php

namespace Tests\Feature\JsonApi\Rent;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RentCreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_create_a_rent()
    {
        /** @var Movie $movie */
        $movie = Movie::factory()->create();

        $route = route('api:v1:rentals.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'rentals',
                'attributes' => [
                    'movie_id' => $movie->getRouteKey(),
                    'days_of_rent' => $this->faker->numberBetween(1, 10)
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_cannot_create_a_rent_without_fields()
    {
        Passport::actingAs(User::factory()->create());

        /** @var Movie $movie */
        $movie = Movie::factory()->create();

        $route = route('api:v1:rentals.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'rentals',
                'attributes' => [
                    'movie_id' => '',
                    'days_of_rent' => ''
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertSee(['pointer' => '\/data\/attributes\/movie_id'])
            ->assertSee(['pointer' => '\/data\/attributes\/days_of_rent']);
    }

    /** @test */
    public function it_tests_cannot_create_a_rent_without_an_existing_movie_id()
    {
        Passport::actingAs(User::factory()->create());

        $route = route('api:v1:rentals.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'rentals',
                'attributes' => [
                    'movie_id' => 1,
                    'days_of_rent' => $this->faker->numberBetween(1, 10),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertSee(['detail' => 'The selected movie id is invalid.'])
            ->assertSee(['pointer' => '\/data\/attributes\/movie_id']);
    }

    /** @test */
    public function it_tests_cannot_create_a_rent_of_a_movie_without_stock()
    {
        Passport::actingAs(User::factory()->create());

        /** @var Movie $movie */
        $movie = Movie::factory()->create(['stock' => 0]);

        $route = route('api:v1:rentals.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'rentals',
                'attributes' => [
                    'movie_id' => $movie->getRouteKey(),
                    'days_of_rent' => $this->faker->numberBetween(1, 10),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertSee(['detail' => 'There is no stock for this movie.'])
            ->assertSee(['pointer' => '\/data\/attributes\/movie_id']);
    }

    /** @test */
    public function it_tests_create_a_rent_behaves_as_expected()
    {
        /** @var User $user */
        Passport::actingAs($user = User::factory()->create());

        /** @var Movie $movie */
        $movie = Movie::factory()->create(['stock' => $this->faker->numberBetween(1, 10)]);

        $route = route('api:v1:rentals.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'rentals',
                'attributes' => [
                    'movie_id' => $movie->getRouteKey(),
                    'days_of_rent' => $this->faker->numberBetween(1, 10),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'data' => [
                'type' => 'rentals',
                'id' => 1,
                'attributes' => [
                    'user_id' => $user->getRouteKey(),
                    'movie_id' => $movie->getRouteKey(),
                ]
            ]
        ]);

    }
}
