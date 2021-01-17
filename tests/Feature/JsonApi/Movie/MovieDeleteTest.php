<?php

namespace Tests\Feature\Http\JsonApi\Movie;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MovieDeleteTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_delete_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->delete(route('api:v1:movies.delete', $movie));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_an_unauthorized_request_cannot_delete_movie()
    {
        Passport::actingAs(
            User::factory()->guest()->create(),
        );

        $movie = Movie::factory()->create();

        $response = $this->delete(route('api:v1:movies.delete', $movie));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_tests_a_movie_that_does_not_exists_cannot_be_deleted()
    {
        Passport::actingAs(
            User::factory()->admin()->create(),
        );

        // not creating a movie

        $response = $this->delete(route('api:v1:movies.delete', 1));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_tests_a_movie_delete_behaves_as_expected()
    {
        Passport::actingAs(
            User::factory()->admin()->create(),
        );

        $movie = Movie::factory()->create();

        $response = $this->delete(route('api:v1:movies.delete', $movie));

        $response->assertNoContent();

        $this->assertDeleted($movie);
    }
}
