<?php

namespace Tests\Feature\JsonApi\Like;

use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeShowTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_a_read_of_a_like_behaves_like_expected()
    {
        $like = Like::factory()->create();

        $route = route('api:v1:likes.read', $like);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
    }
}
