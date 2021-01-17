<?php

namespace Tests\Feature\JsonApi\Like;

use App\Models\Like;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LikeCreateTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_unauthenticated_request_cannot_create_a_like()
    {
        $route = route('api:v1:likes.create');

        $movie = Movie::factory()->create();

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'likes',
                'attributes' => [
                    'user_id' => '', // does not exist but it would fail before it
                    'movie_id' => $movie->getRouteKey(),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_tests_cannot_create_like_that_already_exists()
    {
        $route = route('api:v1:likes.create');

        $movie = Movie::factory()->hasLikes(1)->create();

        $user = User::factory()->create();

        $like = Like::first();

        Passport::actingAs($user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'likes',
                'attributes' => [
                    'user_id' => $like->user->getRouteKey(),
                    'movie_id' => $like->movie->getRouteKey(),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertSee([
            "detail" => "The user already liked the movie.",
            "pointer" => "\/data\/attributes\/user_id",
        ]);
    }

    /** @test */
    public function it_tests_cannot_create_likes_without_valid_data()
    {
        $route = route('api:v1:likes.create');

        $movie = Movie::factory()->create();

        $user = User::factory()->create();

        Passport::actingAs($user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'likes',
                'attributes' => [
                    'user_id' => '',
                    'movie_id' => '',
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertSee([
            "pointer" => "\/data\/attributes\/user_id",
        ])->assertSee([
            "pointer" => "\/data\/attributes\/movie_id",
        ]);
    }

    /** @test */
    public function it_tests_cannot_create_likes_with_user_or_movie_that_does_not_exists()
    {
        $route = route('api:v1:likes.create');

        $movie = Movie::factory()->create();

        $user = User::factory()->create();

        Passport::actingAs($user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'likes',
                'attributes' => [
                    'user_id' => 100,
                    'movie_id' => 100,
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertSee([
            "pointer" => "\/data\/attributes\/user_id",
        ])->assertSee([
            "pointer" => "\/data\/attributes\/movie_id",
        ]);
    }

    /** @test */
    public function it_tests_create_like_behaves_like_expected()
    {
        $route = route('api:v1:likes.create');

        $movie = Movie::factory()->create();

        $user = User::factory()->create();

        Passport::actingAs($user);

        $response = $this->jsonApi()->withJson([
            'data' => [
                'type' => 'likes',
                'attributes' => [
                    'user_id' => $user->getRouteKey(),
                    'movie_id' => $movie->getRouteKey(),
                ]
            ]
        ])->post($route);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}
