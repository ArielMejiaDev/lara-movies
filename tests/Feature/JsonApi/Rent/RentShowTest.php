<?php

namespace Tests\Feature\JsonApi\Rent;

use App\Actions\PenaltyCalculator;
use App\Models\Rent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RentShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_see_rental_show()
    {
        $rent = Rent::factory()->create();

        $route = route('api:v1:rentals.read', $rent);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_see_rental_show()
    {
        Passport::actingAs(User::factory()->guest()->create());

        $rent = Rent::factory()->create();

        $route = route('api:v1:rentals.read', $rent);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests__rental_show_behaves_as_expected()
    {
        Passport::actingAs(User::factory()->admin()->create());

        /** @var Rent $rent */
        $rent = Rent::factory()->create();

        $route = route('api:v1:rentals.read', $rent);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'type' => 'rentals',
                'id' => $rent->getRouteKey(),
                'attributes' => [
                    'user_id' => $rent->user_id,
                    'movie_id' => $rent->movie_id,
                ]
            ]
        ]);
    }

    /** @test */
    public function it_tests__rental_show_with_relationships()
    {
        Passport::actingAs(User::factory()->admin()->create());

        /** @var Rent $rent */
        $rent = Rent::factory()->create();

        $route = route('api:v1:rentals.read', $rent);

        $response = $this->jsonApi()->includePaths('movies,users')->get($route);

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'relationships' => [
                    'users' => [
                        'links' => [
                            'self' => route('api:v1:rentals.relationships.users.read', $rent),
                            'related' =>  route('api:v1:rentals.relationships.users', $rent),
                        ]
                    ],
                    'movies' => [
                        'links' => [
                            'self' => route('api:v1:rentals.relationships.movies.read', $rent),
                            'related' =>  route('api:v1:rentals.relationships.movies', $rent),
                        ]
                    ]
                ],
            ]
        ]);
    }

    /** @test */
    public function it_tests__rental_show_with_movies_and_users_included()
    {
        Passport::actingAs(User::factory()->admin()->create());

        /** @var Rent $rent */
        $rent = Rent::factory()->create();

        $route = route('api:v1:rentals.read', $rent);

        $response = $this->jsonApi()->includePaths('movies,users')->get($route);

        $response->assertOk();

        $response->assertJson([
            'included' => [
                [
                    'type' => 'users',
                    'id' => $rent->user->getRouteKey(),
                ],
                [
                    'type' => 'movies',
                    'id' => $rent->movie->getRouteKey()
                ]
            ]
        ]);
    }

    /** @test */
    public function it_tests_rental_show_penalty_fee_if_movie_returns_late()
    {
        Passport::actingAs(User::factory()->admin()->create());

        $daysOfRent = 5;

        $daysAgo = 10;

        /** @var Rent $rent */
        $rent = Rent::factory()->create([
            'days_of_rent' => $daysOfRent,
            'created_at' => now()->subDays($daysAgo),
        ]);

        $route = route('api:v1:rentals.read', $rent);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertSee(['penalty' => ($daysAgo - $daysOfRent) * PenaltyCalculator::PENALTY_FEE]);
    }

    /** @test */
    public function it_tests_rental_show_does_not_show_penalty_fee_if_movie_returns_at_the_time()
    {
        Passport::actingAs(User::factory()->admin()->create());

        $daysOfRent = 5;

        $daysAgo = 3;

        /** @var Rent $rent */
        $rent = Rent::factory()->create([
            'days_of_rent' => $daysOfRent,
            'created_at' => now()->subDays($daysAgo),
        ]);

        $route = route('api:v1:rentals.read', $rent);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertSee(['penalty' => PenaltyCalculator::NO_PENALTY_TEXT]);
    }
}
