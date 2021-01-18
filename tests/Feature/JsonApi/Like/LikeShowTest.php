<?php

namespace Tests\Feature\JsonApi\Like;

use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_a_read_of_a_specific_like_has_relationships()
    {
        /** @var Like $like */
        $like = Like::factory()->create();

        $route = route('api:v1:likes.read', $like);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJsonFragment([
            'relationships' => [
                'movies' => [
                    'links' => [
                        'self' => route('api:v1:likes.relationships.movies.read', $like),
                        'related' => route('api:v1:likes.relationships.movies', $like),
                    ]
                ],
                'users' => [
                    'links' => [
                        'self' => route('api:v1:likes.relationships.users.read', $like),
                        'related' => route('api:v1:likes.relationships.users', $like),
                    ]
                ],
            ]
        ]);
    }

    /** @test */
    public function it_tests_a_read_of_a_specific_like_has_users_and_movies_includes()
    {
        /** @var Like $like */
        $like = Like::factory()->create();

        $route = route('api:v1:likes.read', $like);

        $response = $this->jsonApi()->includePaths('users,movies')->get($route);

        $response->assertOk();

        $response->assertJson([
            'included' => [
                [
                    'type' => 'users',
                    'id' => $like->user_id,
                    'attributes' => [
                        'name' => $like->user->name,
                    ]
                ],
                [
                    'type' => 'movies',
                    'id' => $like->movie_id,
                    'attributes' => [
                        'title' => $like->movie->title,
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function it_tests_a_read_of_a_like_behaves_like_expected()
    {
        /** @var Like $like */
        $like = Like::factory()->create();

        $route = route('api:v1:likes.read', $like);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'type' => 'likes',
                'id' => $like->getRouteKey(),
                'attributes' => [
                    'createdAt' => $like->created_at->toISOString(),
                    'updatedAt' => $like->updated_at->toISOString(),
                ],
            ],
        ]);
    }
}
