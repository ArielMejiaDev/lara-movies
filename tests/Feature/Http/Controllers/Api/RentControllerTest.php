<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Movie;
use App\Models\Rent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\RentController
 */
class RentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $rents = Rent::factory()->count(3)->create();

        $response = $this->get(route('rent.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\RentController::class,
            'store',
            \App\Http\Requests\Api\RentStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $movie = Movie::factory()->create();
        $user = User::factory()->create();
        $rental_limit_at = $this->faker->dateTime();

        $response = $this->post(route('rent.store'), [
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'rental_limit_at' => $rental_limit_at,
        ]);

        $rents = Rent::query()
            ->where('movie_id', $movie->id)
            ->where('user_id', $user->id)
            ->where('rental_limit_at', $rental_limit_at)
            ->get();
        $this->assertCount(1, $rents);
        $rent = $rents->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $rent = Rent::factory()->create();

        $response = $this->get(route('rent.show', $rent));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\RentController::class,
            'update',
            \App\Http\Requests\Api\RentUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $rent = Rent::factory()->create();
        $movie = Movie::factory()->create();
        $user = User::factory()->create();
        $rental_limit_at = now();

        $response = $this->put(route('rent.update', $rent), [
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'rental_limit_at' => $rental_limit_at,
        ]);

        $rent->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($movie->id, $rent->movie_id);
        $this->assertEquals($user->id, $rent->user_id);

        $this->assertEquals(
            $rental_limit_at->toDateTimeString(),
            Carbon::createFromTimestamp($rent->rental_limit_at)->toDateTimeString()
        );
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $rent = Rent::factory()->create();

        $response = $this->delete(route('rent.destroy', $rent));

        $response->assertNoContent();

        $this->assertDeleted($rent);
    }
}
