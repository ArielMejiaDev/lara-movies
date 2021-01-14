<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $route = route('api.v1.movies.index', [
            'page[size]' => 1,
            'page[number]' => 3
        ]);

        $response = $this->getJson($route);

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertJsonCount(1, 'data');

        $response
            ->assertDontSee($movies->first()->title)
            ->assertDontSee($movies->find(2)->title)
            ->assertSee($movies->find(3)->title);

        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next'],
        ]);

        $response->assertJsonFragment([
            'first' => route('api.v1.movies.index', ['page[size]' => 1, 'page[number]' => 1]),
            'last' => route('api.v1.movies.index', ['page[size]' => 1, 'page[number]' => 3]),
            'prev' => route('api.v1.movies.index', ['page[size]' => 1, 'page[number]' => 2]),
            'next' => null,
        ]);

        $response->assertJsonFragment([
            'data' => [
                [
                    'type' => 'articles',
                    'id' => $movies->find(3)->getRouteKey(),
                    'attributes' => [
                        'title' => $movies->find(3)->title,
                        'description' => $movies->find(3)->description,
                        'image' => $movies->find(3)->image,
                        'stock' => $movies->find(3)->stock,
                        'rental_price' => $movies->find(3)->rental_price,
                        'sale_price' => $movies->find(3)->sale_price,
                        'availability' => $movies->find(3)->availability,
                        'likes' => (int) $movies->find(3)->likes,
                    ],
                    'links' => [
                        'self' => route('api.v1.movies.show', $movies->find(3))
                    ],
                ]
            ],
        ]);
    }

    /** @test */
    public function index_can_be_sort_by_title_asc()
    {
        Movie::factory()->create(['title' => 'B title']);
        Movie::factory()->create(['title' => 'A title']);
        Movie::factory()->create(['title' => 'C title']);

        $route = route('api.v1.movies.index', ['sort' => 'title']);
        $request = $this->getJson($route);

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

        $route = route('api.v1.movies.index', ['sort' => '-title']);
        $request = $this->getJson($route);

        $request->assertSeeInOrder([
            'C title',
            'B title',
            'A title',
        ]);
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
        $this->withoutExceptionHandling();

        $title = $this->faker->sentence(4);
        $description = $this->faker->text;
        $image = $this->faker->word;
        $stock = $this->faker->word;
        $rental_price = $this->faker->word;
        $sale_price = $this->faker->word;
        $availability = $this->faker->boolean;

        $route = route('movies.store');

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

        $response = $this->getJson(route('api.v1.movies.show', $movie));

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
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
                    'self' => route('api.v1.movies.show', $movie)
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
        $movie = Movie::factory()->create();
        $title = $this->faker->sentence(4);
        $description = $this->faker->text;
        $image = $this->faker->word;
        $stock = $this->faker->word;
        $rental_price = $this->faker->word;
        $sale_price = $this->faker->word;
        $availability = $this->faker->boolean;

        $response = $this->put(route('movies.update', $movie), [
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

        $response = $this->delete(route('movies.destroy', $movie));

        $response->assertNoContent();

        $this->assertDeleted($movie);
    }
}
