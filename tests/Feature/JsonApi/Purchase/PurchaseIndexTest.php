<?php

namespace Tests\Feature\JsonApi\Purchase;

use App\Models\Movie;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseIndexTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_tests_a_purchase_index_behaves_as_expected()
    {
        $movies = Movie::factory()->times(5)->hasPurchases(3)->create();

        $route = route('api:v1:purchases.index');

        $response = $this->jsonApi()->get($route);

        $response->assertOk();

        $response->assertJsonCount(15, 'data');

        $response->assertSee(['amount' => $movies->first()->purchases->first()->amount]);
    }
}
