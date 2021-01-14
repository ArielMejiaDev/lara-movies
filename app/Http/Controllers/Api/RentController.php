<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RentStoreRequest;
use App\Http\Requests\Api\RentUpdateRequest;
use App\Http\Resources\Api\RentCollection;
use App\Http\Resources\Api\RentResource;
use App\Models\Rent;
use Illuminate\Http\Request;

class RentController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Api\RentCollection
     */
    public function index(Request $request)
    {
        $rents = Rent::all();

        return new RentCollection($rents);
    }

    /**
     * @param \App\Http\Requests\Api\RentStoreRequest $request
     * @return \App\Http\Resources\Api\RentResource
     */
    public function store(RentStoreRequest $request)
    {
        $rent = Rent::create($request->validated());

        return new RentResource($rent);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rent $rent
     * @return \App\Http\Resources\Api\RentResource
     */
    public function show(Request $request, Rent $rent)
    {
        return new RentResource($rent);
    }

    /**
     * @param \App\Http\Requests\Api\RentUpdateRequest $request
     * @param \App\Models\Rent $rent
     * @return \App\Http\Resources\Api\RentResource
     */
    public function update(RentUpdateRequest $request, Rent $rent)
    {
        $rent->update($request->validated());

        return new RentResource($rent);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Rent $rent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Rent $rent)
    {
        $rent->delete();

        return response()->noContent();
    }
}
