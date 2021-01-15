<?php

namespace Tests\Feature\Http\JsonApi\Movie;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieShowTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function it_test_show_behaves_as_expected()
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
}
