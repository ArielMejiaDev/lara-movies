<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Penalty;
use App\Models\Rent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PenaltyController
 */
class PenaltyControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $penalties = Penalty::factory()->count(3)->create();

        $response = $this->get(route('penalty.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\PenaltyController::class,
            'store',
            \App\Http\Requests\Api\PenaltyStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $rent = Rent::factory()->create();
        $amount = $this->faker->word;

        $response = $this->post(route('penalty.store'), [
            'rent_id' => $rent->id,
            'amount' => $amount,
        ]);

        $penalties = Penalty::query()
            ->where('rent_id', $rent->id)
            ->where('amount', $amount)
            ->get();
        $this->assertCount(1, $penalties);
        $penalty = $penalties->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $penalty = Penalty::factory()->create();

        $response = $this->get(route('penalty.show', $penalty));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\PenaltyController::class,
            'update',
            \App\Http\Requests\Api\PenaltyUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $penalty = Penalty::factory()->create();
        $rent = Rent::factory()->create();
        $amount = $this->faker->word;

        $response = $this->put(route('penalty.update', $penalty), [
            'rent_id' => $rent->id,
            'amount' => $amount,
        ]);

        $penalty->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($rent->id, $penalty->rent_id);
        $this->assertEquals($amount, $penalty->amount);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $penalty = Penalty::factory()->create();

        $response = $this->delete(route('penalty.destroy', $penalty));

        $response->assertNoContent();

        $this->assertDeleted($penalty);
    }
}
