<?php

namespace Tests\Feature\JsonApi\Like;

use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LikeDeleteTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_a_unauthenticated_request_cannot_delete_a_like()
    {
        $like = Like::factory()->create();

        $route = route('api:v1:likes.delete', $like->getRouteKey());

        $response = $this->jsonApi()->delete($route);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_test_a_like_cannot_be_deleted_with_invalid_data()
    {
        Passport::actingAs(User::factory()->create());

        // no like created

        $route = route('api:v1:likes.delete', 1);

        $response = $this->jsonApi()->delete($route);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_tests_a_delete_like_behaves_as_expected()
    {
        Passport::actingAs(User::factory()->create());

        $like = Like::factory()->create();

        $route = route('api:v1:likes.delete', $like);

        $response = $this->jsonApi()->delete($route);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
