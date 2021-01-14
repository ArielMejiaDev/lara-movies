<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PenaltyStoreRequest;
use App\Http\Requests\Api\PenaltyUpdateRequest;
use App\Http\Resources\Api\PenaltyCollection;
use App\Http\Resources\Api\PenaltyResource;
use App\Models\Penalty;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Api\PenaltyCollection
     */
    public function index(Request $request)
    {
        $penalties = Penalty::all();

        return new PenaltyCollection($penalties);
    }

    /**
     * @param \App\Http\Requests\Api\PenaltyStoreRequest $request
     * @return \App\Http\Resources\Api\PenaltyResource
     */
    public function store(PenaltyStoreRequest $request)
    {
        $penalty = Penalty::create($request->validated());

        return new PenaltyResource($penalty);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Penalty $penalty
     * @return \App\Http\Resources\Api\PenaltyResource
     */
    public function show(Request $request, Penalty $penalty)
    {
        return new PenaltyResource($penalty);
    }

    /**
     * @param \App\Http\Requests\Api\PenaltyUpdateRequest $request
     * @param \App\Models\Penalty $penalty
     * @return \App\Http\Resources\Api\PenaltyResource
     */
    public function update(PenaltyUpdateRequest $request, Penalty $penalty)
    {
        $penalty->update($request->validated());

        return new PenaltyResource($penalty);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Penalty $penalty
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Penalty $penalty)
    {
        $penalty->delete();

        return response()->noContent();
    }
}
