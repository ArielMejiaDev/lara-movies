<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Like;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\LikeController
 */
class LikeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $likes = Like::factory()->count(3)->create();

        $response = $this->get(route('like.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\LikeController::class,
            'store',
            \App\Http\Requests\Api\LikeStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $movie = Movie::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('like.store'), [
            'movie_id' => $movie->id,
            'user_id' => $user->id,
        ]);

        $likes = Like::query()
            ->where('movie_id', $movie->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $likes);
        $like = $likes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $like = Like::factory()->create();

        $response = $this->get(route('like.show', $like));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\LikeController::class,
            'update',
            \App\Http\Requests\Api\LikeUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $like = Like::factory()->create();
        $movie = Movie::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('like.update', $like), [
            'movie_id' => $movie->id,
            'user_id' => $user->id,
        ]);

        $like->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($movie->id, $like->movie_id);
        $this->assertEquals($user->id, $like->user_id);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $like = Like::factory()->create();

        $response = $this->delete(route('like.destroy', $like));

        $response->assertNoContent();

        $this->assertDeleted($like);
    }
}
