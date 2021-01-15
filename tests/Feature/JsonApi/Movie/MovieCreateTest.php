<?php

namespace Tests\Feature\Http\JsonApi\Movie;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MovieCreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_store_a_movie()
    {
        $movie = Movie::factory()->raw();

        $this->assertDatabaseMissing('movies', $movie);

        $route = route('api:v1:movies.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'movies',
                'attributes' => $movie,
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonStructure([]);

        $this->assertDatabaseMissing('movies', $movie);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_store_a_movie()
    {
        Passport::actingAs(User::factory()->guest()->create());

        $movie = Movie::factory()->raw();

        $this->assertDatabaseMissing('movies', $movie);

        $route = route('api:v1:movies.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'movies',
                'attributes' => $movie,
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonStructure([]);

        $this->assertDatabaseMissing('movies', $movie);
    }

    /** @test */
    public function it_tests_a_movie_fields_are_required_to_be_stored()
    {
        Passport::actingAs(User::factory()->admin()->create());

        $movie = Movie::factory()->raw([
            'title' => '',
            'description' => '',
            'image' => '',
            'stock' => '',
            'rental_price' => '',
            'sale_price' => '',
            'availability' => '',
        ]);

        $response = $this->jsonApi()
            ->withJson([
                'data' => [
                    'type' => 'movies',
                    'attributes' => $movie,
                ]
            ])
            ->post(route('api:v1:movies.create'));

        // I prefer to test the pointer, but I show here an example of how to tests the validation messages.
        // If the app need translations you can use the helper trans(fileName, arrayKey), here is not the case.

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee(['detail' => 'The title field is required.'])
            ->assertSee(['detail' => 'The description field is required.'])
            ->assertSee(['detail' => 'The image field is required.'])
            ->assertSee(['detail' => 'The stock field is required.'])
            ->assertSee(['detail' => 'The rental price field is required.'])
            ->assertSee(['detail' => 'The sale price field is required.'])
            ->assertSee(['detail' => 'The availability field is required.']);

        $this->assertDatabaseMissing('movies', $movie);
    }

    /** @test */
    public function it_tests_a_movie_title_must_be_unique_to_be_stored()
    {
        Passport::actingAs(User::factory()->admin()->create());

        Movie::factory()->create([
            'title' => 'The Avengers.'
        ]);

        /** @var Movie $newMovie */
        $newMovie = Movie::factory()->raw([
            'title' => 'The Avengers.'
        ]);

        $response = $this->jsonApi()
            ->withJson([
                'data' => [
                    'type' => 'movies',
                    'attributes' => $newMovie,
                ]
            ])
            ->post(route('api:v1:movies.create'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee(['pointer' => '\/data\/attributes\/title']);
    }

    /** @test */
    public function it_tests_a_movie_store_behaves_as_expected()
    {
        Passport::actingAs(User::factory()->admin()->create());

        $movie = Movie::factory()->raw();

        $this->assertDatabaseMissing('movies', $movie);

        $route = route('api:v1:movies.create');

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'movies',
                'attributes' => $movie,
            ]
        ])->post($route);

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas('movies', $movie);
    }

}
