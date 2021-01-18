<?php

namespace Tests\Feature\Http\JsonApi\Movie;

use App\Models\Like;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_test_show_behaves_as_expected()
    {
        $movie = Movie::factory()->create();

        $response = $this->jsonApi()->get(route('api:v1:movies.read', $movie));

        $response->assertOk();
        $response->assertJsonStructure([]);

        $response->assertJson([
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
                    'likes_counter' => $movie->likes()->count(),
                    'liked_by_user' => 0,
                ],
                'links' => [
                    'self' => route('api:v1:movies.read', $movie)
                ],
            ]
        ]);
    }

    /** @test */
    public function it_test_show_can_include_likes_relationship()
    {
        /** @var Movie $movie */
        $movie = Movie::factory()->create();

        /** @var Like $like */
        $like = Like::factory()->create(['movie_id' => $movie->id]);

        $route = route('api:v1:movies.read', $movie);

        $response = $this->jsonApi()->includePaths('likes')->get($route);

        $response->assertSee($like->id);

        $response->assertJson([
            'included' => [
                [
                    'type' => 'likes',
                    'id' => $like->id,
                ]
            ]
        ]);
    }
}
