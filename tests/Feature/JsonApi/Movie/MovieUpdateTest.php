<?php

namespace Tests\Feature\Http\JsonApi\Movie;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MovieUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_update_a_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->jsonApi()
            ->withJson([
                'data' => [
                    'type' => 'movies',
                    'id' => $movie->id,
                    'attributes' => [
                        'title' => '',
                        'description' => '',
                        'image' => '',
                        'stock' => '',
                        'rental_price' => '',
                        'sale_price' => '',
                        'availability' => '',
                    ]
                ]
            ])
            ->patch(route('api:v1:movies.update', $movie));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_update_a_movie()
    {
        Passport::actingAs($user = User::factory()->guest()->create());

        $movie = Movie::factory()->create();

        $response = $this->jsonApi()
            ->withJson([
                'data' => [
                    'type' => 'movies',
                    'id' => $movie->id,
                    'attributes' => [
                        'title' => '',
                        'description' => '',
                        'image' => '',
                        'stock' => '',
                        'rental_price' => '',
                        'sale_price' => '',
                        'availability' => '',
                    ]
                ]
            ])
            ->patch(route('api:v1:movies.update', $movie));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_a_movie_fields_are_required_to_be_updated()
    {
        Passport::actingAs(User::factory()->admin()->create());

        $movie = Movie::factory()->create();
        $title = '';
        $description = '';
        $image = '';
        $stock = '';
        $rental_price = '';
        $sale_price = '';
        $availability = '';

        $response = $this->jsonApi()
            ->withJson([
                'data' => [
                    'type' => 'movies',
                    'id' => $movie->id,
                    'attributes' => [
                        'title' => $title,
                        'description' => $description,
                        'image' => $image,
                        'stock' => $stock,
                        'rental_price' => $rental_price,
                        'sale_price' => $sale_price,
                        'availability' => $availability,
                    ]
                ]
            ])
            ->patch(route('api:v1:movies.update', $movie));

        $movie->refresh();

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSee(['pointer' => '\/data\/attributes\/title'])
            ->assertSee(['pointer' => '\/data\/attributes\/description'])
            ->assertSee(['pointer' => '\/data\/attributes\/image'])
            ->assertSee(['pointer' => '\/data\/attributes\/stock'])
            ->assertSee(['pointer' => '\/data\/attributes\/rental_price'])
            ->assertSee(['pointer' => '\/data\/attributes\/sale_price'])
            ->assertSee(['pointer' => '\/data\/attributes\/availability'])
            ->assertJsonStructure([]);

    }

    /** @test */
    public function it_tests_a_movie_title_must_be_unique_except_by_the_same_model_to_be_updated()
    {
        Passport::actingAs(User::factory()->admin()->create());

        /** @var Movie $movie */

        $movie = Movie::factory()->create(['title' => 'The Avengers.']);

        $response = $this->jsonApi()
            ->withJson([
                'data' => [
                    'type' => 'movies',
                    'id' => $movie->id,
                    'attributes' => [
                        'title' => $movie->title,
                    ]
                ]
            ])
            ->patch(route('api:v1:movies.update', $movie));

        $response->assertOk()
            ->assertSee(['title' => 'The Avengers.'])
            ->assertJsonStructure([]);
    }

    /** @test */
    public function it_tests_a_movie_update_behaves_as_expected()
    {
        Passport::actingAs(User::factory()->admin()->create());

        /** @var Movie $movie */
        $movie = Movie::factory()->create();

        $title = $this->faker->sentence(4);
        $description = $this->faker->text;
        $image = $this->faker->word;
        $stock = $this->faker->numberBetween(25, 50);
        $rental_price = $this->faker->numberBetween(5, 15);
        $sale_price = $this->faker->numberBetween(30, 60);
        $availability = $this->faker->boolean;

        $response = $this->jsonApi()
            ->withJson([
                'data' => [
                    'type' => 'movies',
                    'id' => $movie->id,
                    'attributes' => [
                        'title' => $title,
                        'description' => $description,
                        'image' => $image,
                        'stock' => $stock,
                        'rental_price' => $rental_price,
                        'sale_price' => $sale_price,
                        'availability' => $availability,
                    ]
                ]
            ])
            ->patch(route('api:v1:movies.update', $movie));

        $movie->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $movie->title);
        $this->assertEquals($description, $movie->description);
        $this->assertEquals($image, $movie->image);
        $this->assertEquals($stock, $movie->stock);
        $this->assertEquals(number_format($rental_price, 2), $movie->rental_price);
        $this->assertEquals(number_format($sale_price, 2), $movie->sale_price);
        $this->assertEquals($availability, $movie->availability);
    }

}
