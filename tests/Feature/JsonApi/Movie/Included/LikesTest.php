<?php

namespace Tests\Feature\JsonApi\Movie\Included;

use App\Models\Like;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_test_movies_likes_index_relationship_links()
    {
        /** @var Movie $movie */
        $movie = Like::factory()->create();

        /** @var Like $like */
        $like = Like::factory()->create(['movie_id' => $movie->id]);

        $route = route('api:v1:movies.relationships.likes', $movie);

        $response = $this->jsonApi()->get($route);

        $response->assertSee($like->id);
    }

    /** @test */
    public function it_test_movies_likes_index_relationship_links_does_not_include_data_by_default()
    {
        /** @var Movie $movie */
        $movie = Like::factory()->create();

        /** @var Like $like */
        $like = Like::factory()->create(['movie_id' => $movie->id]);

        $route = route('api:v1:movies.relationships.likes', $movie);

        $response = $this->jsonApi()->get($route);

        $response->assertSee($like->id);

        $response->assertSee(['createdAt' => $like->created_at->toISOString()]);

        $response->assertSee($like->updated_at->toISOString());


        $this->assertNull($response->json('data.relationships.likes.data'));
    }

    /** @test */
    public function it_test_movies_likes_show_relationship_links()
    {
        /** @var Movie $movie */
        $movie = Like::factory()->create();

        /** @var Like $like */
        $like = Like::factory()->create(['movie_id' => $movie->id]);

        $route = route('api:v1:movies.relationships.likes.read', $movie);

        $response = $this->jsonApi()->get($route);

        $response->assertSee($like->id);
    }
}
