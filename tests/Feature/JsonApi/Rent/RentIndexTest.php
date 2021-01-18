<?php

namespace Tests\Feature\JsonApi\Rent;

use App\Models\Rent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RentIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_see_rentals()
    {
        $route = route('api:v1:rentals.index');

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_see_rentals()
    {
        Passport::actingAs(User::factory()->guest()->create());

        $route = route('api:v1:rentals.index');

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_rentals_index_behaves_as_expected()
    {
        Passport::actingAs(User::factory()->admin()->create());

        /** @var Rent $rent */
        $rent = Rent::factory()->create();

        $route = route('api:v1:rentals.index');

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'data' => [
                [
                    'type' => 'rentals',
                    'id' => $rent->getRouteKey(),
                    'attributes' => [
                        'user_id' => $rent->user_id,
                        'movie_id' => $rent->movie_id,
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function it_tests_rentals_index_pagination_works_as_expected()
    {
        Passport::actingAs(User::factory()->admin()->create());

        /** @var Rent $rent */
        Rent::factory()->times(15)->create();

        $route = route('api:v1:rentals.index', [
            'page[size]' => 5,
            'page[number]' => 2,
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'links' => [
                'first' => route('api:v1:rentals.index', ['page[number]' => 1, 'page[size]' => 5]),
                'prev' => route('api:v1:rentals.index', ['page[number]' => 1, 'page[size]' => 5]),
                'next' => route('api:v1:rentals.index', ['page[number]' => 3, 'page[size]' => 5]),
                'last' => route('api:v1:rentals.index', ['page[number]' => 3, 'page[size]' => 5]),
            ]
        ]);
    }
}
