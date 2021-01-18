<?php

namespace Tests\Feature\JsonApi\Purchase;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PurchaseIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_see_purchase_index()
    {
        Movie::factory()->times(5)->hasPurchases(3)->create();

        $route = route('api:v1:purchases.index');

        $response = $this->jsonApi()->get($route);

        $response->assertSee(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_see_purchase_index()
    {
        Passport::actingAs(User::factory()->guest()->create());

        Movie::factory()->times(5)->hasPurchases(3)->create();

        $route = route('api:v1:purchases.index');

        $response = $this->jsonApi()->get($route);

        $response->assertSee(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_purchase_index_work_with_pagination()
    {
        Passport::actingAs(User::factory()->admin()->create());

        Movie::factory()->times(5)->hasPurchases(3)->create();

        $route = route('api:v1:purchases.index', [
            'page[number]' => 2,
            'page[size]' => 3,
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertJsonFragment([
            'links' => [
                'first' => route('api:v1:purchases.index', [
                    'page[number]' => 1,
                    'page[size]' => 3,
                ]),
                'prev' => route('api:v1:purchases.index', [
                    'page[number]' => 1,
                    'page[size]' => 3,
                ]),
                'next' => route('api:v1:purchases.index', [
                    'page[number]' => 3,
                    'page[size]' => 3,
                ]),
                'last' => route('api:v1:purchases.index', [
                    'page[number]' => 5,
                    'page[size]' => 3,
                ]),
            ]
        ]);
    }

    /** @test */
    public function it_tests_a_purchase_index_behaves_as_expected()
    {
        Passport::actingAs(User::factory()->admin()->create());

        $movies = Movie::factory()->times(5)->hasPurchases(3)->create();

        $route = route('api:v1:purchases.index');

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJsonCount(15, 'data');

        $response->assertSee(['amount' => $movies->first()->purchases->first()->amount]);
    }
}
