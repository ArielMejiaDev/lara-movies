<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Movie;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Testing\MakesJsonApiRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use JMac\Testing\Traits\AdditionalAssertions;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\MovieController
 */
class MovieControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /** @test */
    public function index_behaves_as_expected()
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

//        $response->assertJsonStructure([
//            'links' => ['first', 'last', 'prev', 'next'],
//        ]);

//        $response->assertJsonFragment([
//            'first' => route('api:v1:movies.index', ['page[size]' => 1, 'page[number]' => 1]),
//            'last' => route('api:v1:movies.index', ['page[size]' => 1, 'page[number]' => 3]),
//            'prev' => route('api:v1:movies.index', ['page[size]' => 1, 'page[number]' => 2]),
//            'next' => null,
//        ]);

//        $response->assertJsonFragment([
//            'data' => [
//                [
//                    'type' => 'articles',
//                    'id' => $movies->find(3)->getRouteKey(),
//                    'attributes' => [
//                        'title' => $movies->find(3)->title,
//                        'description' => $movies->find(3)->description,
//                        'image' => $movies->find(3)->image,
//                        'stock' => $movies->find(3)->stock,
//                        'rental_price' => $movies->find(3)->rental_price,
//                        'sale_price' => $movies->find(3)->sale_price,
//                        'availability' => $movies->find(3)->availability,
//                        'likes' => (int) $movies->find(3)->likes,
//                    ],
//                    'links' => [
//                        'self' => route('api:v1:movies.show', $movies->find(3))
//                    ],
//                ]
//            ],
//        ]);
    }

    /** @test */
    public function index_can_be_sort_by_title_asc()
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
    public function index_can_be_sort_by_title_desc()
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
    public function index_can_be_filter_by_title()
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
    public function index_cannot_be_filter_by_not_existing_filter()
    {
        $movies = Movie::factory()->times(2)->create();

        $route = route('api:v1:movies.index', [
            'filter[someColumn]' => 'Any unrelated text',
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertStatus(400);

    }

    /** @test */
    public function index_can_be_filter_by_availability()
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
    public function index_can_be_filter_by_availability_with_truly_and_falsy_values()
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
    public function index_can_be_filter_by_availability_with_string_values()
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
    public function index_can_be_filter_only_by_admins()
    {
        $this->markTestSkipped();
    }

    /** @test */
    public function index_cannot_be_filter_only_by_guests()
    {
        $this->markTestSkipped();
    }

    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\MovieController::class,
            'store',
            \App\Http\Requests\Api\MovieStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $this->markTestIncomplete();

        $title = $this->faker->sentence(4);
        $description = $this->faker->text;
        $image = $this->faker->word;
        $stock = $this->faker->word;
        $rental_price = $this->faker->word;
        $sale_price = $this->faker->word;
        $availability = $this->faker->boolean;

        $route = route('api:v1:movies.create');

        $response = $this->post($route, [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'stock' => $stock,
            'rental_price' => $rental_price,
            'sale_price' => $sale_price,
            'availability' => $availability,
        ]);

        $movies = Movie::query()
            ->where('title', $title)
            ->where('description', $description)
            ->where('image', $image)
            ->where('stock', $stock)
            ->where('rental_price', $rental_price)
            ->where('sale_price', $sale_price)
            ->where('availability', $availability)
            ->get();

        $this->assertCount(1, $movies);
        $movie = $movies->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $movie = Movie::factory()->create();

        $response = $this->jsonApi()->get(route('api:v1:movies.read', $movie));

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertExactJson([
            'data' => [
                'type' => 'movies',
                'id' => $movie->getRouteKey(),
                'attributes' => [
                    'title' => $movie->title,
                    'description' => $movie->description,
                    'image' => $movie->image,
                    'stock' => $movie->stock,
                    'rental_price' => $movie->rental_price,
                    'sale_price' => $movie->sale_price,
                    'availability' => $movie->availability,
                    'likes' => $movie->likes,
                ],
                'links' => [
                    'self' => route('api:v1:movies.read', $movie)
                ],
            ]
        ]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\MovieController::class,
            'update',
            \App\Http\Requests\Api\MovieUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $this->markTestIncomplete();

        $movie = Movie::factory()->create();
        $title = $this->faker->sentence(4);
        $description = $this->faker->text;
        $image = $this->faker->word;
        $stock = $this->faker->word;
        $rental_price = $this->faker->word;
        $sale_price = $this->faker->word;
        $availability = $this->faker->boolean;

        $this->withoutExceptionHandling();

        $response = $this->patch(route('api:v1:movies.update', $movie), [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'stock' => $stock,
            'rental_price' => $rental_price,
            'sale_price' => $sale_price,
            'availability' => $availability,
        ]);

        $movie->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $movie->title);
        $this->assertEquals($description, $movie->description);
        $this->assertEquals($image, $movie->image);
        $this->assertEquals($stock, $movie->stock);
        $this->assertEquals($rental_price, $movie->rental_price);
        $this->assertEquals($sale_price, $movie->sale_price);
        $this->assertEquals($availability, $movie->availability);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $movie = Movie::factory()->create();

        $response = $this->delete(route('api:v1:movies.delete', $movie));

        $response->assertNoContent();

        $this->assertDeleted($movie);
    }
}
