<?php

namespace Tests\Feature\JsonApi\Like;

use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeIndexTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_index_of_likes()
    {
        Like::factory()->times(3)->create();

        $route = route('api:v1:likes.index');

        $response = $this->jsonApi()->get($route);

        $response->assertOk();
    }
}
