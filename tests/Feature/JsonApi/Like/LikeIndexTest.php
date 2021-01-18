<?php

namespace Tests\Feature\JsonApi\Like;

use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_an_index_of_likes_work_with_pagination()
    {
        Like::factory()->times(15)->create();

        $route = route('api:v1:likes.index', [
            'page[size]' => 5,
            'page[number]' => 1,
        ]);

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'links' => [
                'first' => route('api:v1:likes.index', ['page[number]' => 1, 'page[size]' => 5]),
                'next' => route('api:v1:likes.index', ['page[number]' => 2, 'page[size]' => 5]),
                'last' => route('api:v1:likes.index', ['page[number]' => 3, 'page[size]' => 5]),
            ],
        ]);
    }

    /** @test */
    public function it_tests_an_index_of_likes()
    {
        $likes = Like::factory()->times(3)->create();

        $route = route('api:v1:likes.index');

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJson([
            'data' => [
                [
                    'type' => 'likes',
                    'id' => $likes->first()->getRouteKey(),
                    'attributes' => [
                        'createdAt' => $likes->first()->created_at->toISOString(),
                        'updatedAt' => $likes->first()->updated_at->toISOString(),
                    ]
                ]
            ]
        ]);
    }
}
