<?php

namespace Tests\Feature\Http\JsonApi\Movie;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MovieIndexTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_test_index_behaves_as_expected()
    {
        $movies = Movie::factory()->count(3)->create();

        $route = route('api:v1:movies.index', [
            'page[size]' => 1,
            'page[number]' => 3
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertJsonCount(1, 'data');

        $response
            ->assertDontSee($movies->first()->title)
            ->assertDontSee($movies->find(2)->title)
            ->assertSee($movies->find(3)->title);
    }

    /** @test */
    public function it_test_index_can_be_sort_by_title_asc()
    {
        Movie::factory()->create(['title' => 'B title']);
        Movie::factory()->create(['title' => 'A title']);
        Movie::factory()->create(['title' => 'C title']);

        $route = route('api:v1:movies.index', ['sort' => 'title']);

        $request = $this->jsonApi()->get($route);

        $request->assertSeeInOrder([
            'A title',
            'B title',
            'C title',
        ]);
    }

    /** @test */
    public function it_test_index_can_be_sort_by_title_desc()
    {
        Movie::factory()->create(['title' => 'B title']);
        Movie::factory()->create(['title' => 'A title']);
        Movie::factory()->create(['title' => 'C title']);

        $route = route('api:v1:movies.index', ['sort' => '-title']);
        $request = $this->jsonApi()->get($route);

        $request->assertSeeInOrder([
            'C title',
            'B title',
            'A title',
        ]);
    }

    /** @test */
    public function it_test_index_can_be_filter_by_title()
    {
        $this->withoutExceptionHandling();

        $movies = [
            Movie::factory()->create([
                'title' => 'Avengers End Game',
            ]),

            Movie::factory()->create([
                'title' => 'Captain America The Winter Soldier',
            ]),

            Movie::factory()->create([
                'title' => 'Avengers Infinity War',
            ]),
        ];

        $route = route('api:v1:movies.index', [
            'filter[title]' => 'Captain',
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertJsonCount(1, 'data');

        $response
            ->assertDontSee($movies[0]->title)
            ->assertDontSee($movies[2]->title)
            ->assertSee($movies[1]->title);

        $response->assertJsonFragment([
            'data' => [
                [
                    'type' => 'movies',
                    'id' => $movies[1]->getRouteKey(),
                    'attributes' => [
                        'title' => $movies[1]->title,
                        'description' => $movies[1]->description,
                        'image' => $movies[1]->image,
                        'stock' => $movies[1]->stock,
                        'rental_price' => $movies[1]->rental_price,
                        'sale_price' => $movies[1]->sale_price,
                        'availability' => $movies[1]->availability,
                        'likes' => (int) $movies[1]->likes,
                    ],
                    'links' => [
                        'self' => route('api:v1:movies.read', $movies[1])
                    ],
                ]
            ],
        ]);
    }

    /** @test */
    public function it_test_index_cannot_be_filter_by_an_invalid_filter()
    {
        $movies = Movie::factory()->times(2)->create();

        $route = route('api:v1:movies.index', [
            'filter[someColumn]' => 'Any unrelated text',
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(400);

    }

    /** @test */
    public function it_test_index_can_be_filter_by_availability()
    {
        $movies = [
            Movie::factory()->create([
                'availability' => true,
            ]),
            Movie::factory()->create([
                'availability' => false,
            ]),
            Movie::factory()->create([
                'availability' => false,
            ]),
        ];

        $route = route('api:v1:movies.index', [
            'filter[availability]' => true,
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertJsonCount(1, 'data');

        $response
            ->assertDontSee($movies[1]->title)
            ->assertDontSee($movies[2]->title)
            ->assertSee($movies[0]->title);

        $response->assertJsonFragment([
            'data' => [
                [
                    'type' => 'movies',
                    'id' => $movies[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $movies[0]->title,
                        'description' => $movies[0]->description,
                        'image' => $movies[0]->image,
                        'stock' => $movies[0]->stock,
                        'rental_price' => $movies[0]->rental_price,
                        'sale_price' => $movies[0]->sale_price,
                        'availability' => $movies[0]->availability,
                        'likes' => (int) $movies[0]->likes,
                    ],
                    'links' => [
                        'self' => route('api:v1:movies.read', $movies[0])
                    ],
                ]
            ],
        ]);
    }

    /** @test */
    public function it_test_index_can_be_filter_by_availability_with_truly_and_falsy_values()
    {
        $movies = [
            Movie::factory()->create([
                'availability' => true,
            ]),
            Movie::factory()->create([
                'availability' => false,
            ]),
            Movie::factory()->create([
                'availability' => false,
            ]),
        ];

        $route = route('api:v1:movies.index', [
            'filter[availability]' => 1,
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertJsonCount(1, 'data');

        $response
            ->assertDontSee($movies[1]->title)
            ->assertDontSee($movies[2]->title)
            ->assertSee($movies[0]->title);

        $response->assertJsonFragment([
            'data' => [
                [
                    'type' => 'movies',
                    'id' => $movies[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $movies[0]->title,
                        'description' => $movies[0]->description,
                        'image' => $movies[0]->image,
                        'stock' => $movies[0]->stock,
                        'rental_price' => $movies[0]->rental_price,
                        'sale_price' => $movies[0]->sale_price,
                        'availability' => $movies[0]->availability,
                        'likes' => (int) $movies[0]->likes,
                    ],
                    'links' => [
                        'self' => route('api:v1:movies.read', $movies[0])
                    ],
                ]
            ],
        ]);
    }

    /** @test */
    public function it_test_index_can_be_filter_by_availability_with_string_values()
    {
        $movies = [
            Movie::factory()->create([
                'availability' => false,
            ]),
            Movie::factory()->create([
                'availability' => true,
            ]),
            Movie::factory()->create([
                'availability' => true,
            ]),
        ];

        $route = route('api:v1:movies.index', [
            'filter[availability]' => 'false',
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertJsonCount(1, 'data');

        $response
            ->assertDontSee($movies[1]->title)
            ->assertDontSee($movies[2]->title)
            ->assertSee($movies[0]->title);

        $response->assertJsonFragment([
            'data' => [
                [
                    'type' => 'movies',
                    'id' => $movies[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $movies[0]->title,
                        'description' => $movies[0]->description,
                        'image' => $movies[0]->image,
                        'stock' => $movies[0]->stock,
                        'rental_price' => $movies[0]->rental_price,
                        'sale_price' => $movies[0]->sale_price,
                        'availability' => $movies[0]->availability,
                        'likes' => (int) $movies[0]->likes,
                    ],
                    'links' => [
                        'self' => route('api:v1:movies.read', $movies[0])
                    ],
                ]
            ],
        ]);
    }

    /** @test */
    public function it_test_index_cannot_be_filter_by_unauthenticated_request()
    {
        Movie::factory()->times(10)->create();

        $route = route('api:v1:movies.index', ['filter[availability]' => false]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
    }

    /** @test */
    public function it_test_index_cannot_be_filter_by_availability_by_guests()
    {
        Movie::factory()->times(10)->create();

        Passport::actingAs(User::factory()->guest()->create());

        $route = route('api:v1:movies.index', ['filter[availability]' => false]);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_test_index_can_be_filter_by_availability_only_by_admins()
    {
        $movies = Movie::factory()->times(10)->create();

        Passport::actingAs(User::factory()->admin()->create());

        $route = route('api:v1:movies.index', ['filter[availability]' => false]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertSee(['id' => Movie::where('availability', false)->first()->getRouteKey(),]);

    }

}
