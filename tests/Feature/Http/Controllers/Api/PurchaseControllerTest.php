<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Movie;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PurchaseController
 */
class PurchaseControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $purchases = Purchase::factory()->count(3)->create();

        $response = $this->get(route('purchase.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\PurchaseController::class,
            'store',
            \App\Http\Requests\Api\PurchaseStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $movie = Movie::factory()->create();
        $user = User::factory()->create();
        $amount = $this->faker->word;

        $response = $this->post(route('purchase.store'), [
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        $purchases = Purchase::query()
            ->where('movie_id', $movie->id)
            ->where('user_id', $user->id)
            ->where('amount', $amount)
            ->get();
        $this->assertCount(1, $purchases);
        $purchase = $purchases->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $purchase = Purchase::factory()->create();

        $response = $this->get(route('purchase.show', $purchase));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\PurchaseController::class,
            'update',
            \App\Http\Requests\Api\PurchaseUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $purchase = Purchase::factory()->create();
        $movie = Movie::factory()->create();
        $user = User::factory()->create();
        $amount = $this->faker->word;

        $response = $this->put(route('purchase.update', $purchase), [
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        $purchase->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($movie->id, $purchase->movie_id);
        $this->assertEquals($user->id, $purchase->user_id);
        $this->assertEquals($amount, $purchase->amount);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $purchase = Purchase::factory()->create();

        $response = $this->delete(route('purchase.destroy', $purchase));

        $response->assertNoContent();

        $this->assertDeleted($purchase);
    }
}
