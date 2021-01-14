<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PurchaseStoreRequest;
use App\Http\Requests\Api\PurchaseUpdateRequest;
use App\Http\Resources\Api\PurchaseCollection;
use App\Http\Resources\Api\PurchaseResource;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Api\PurchaseCollection
     */
    public function index(Request $request)
    {
        $purchases = Purchase::all();

        return new PurchaseCollection($purchases);
    }

    /**
     * @param \App\Http\Requests\Api\PurchaseStoreRequest $request
     * @return \App\Http\Resources\Api\PurchaseResource
     */
    public function store(PurchaseStoreRequest $request)
    {
        $purchase = Purchase::create($request->validated());

        return new PurchaseResource($purchase);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Purchase $purchase
     * @return \App\Http\Resources\Api\PurchaseResource
     */
    public function show(Request $request, Purchase $purchase)
    {
        return new PurchaseResource($purchase);
    }

    /**
     * @param \App\Http\Requests\Api\PurchaseUpdateRequest $request
     * @param \App\Models\Purchase $purchase
     * @return \App\Http\Resources\Api\PurchaseResource
     */
    public function update(PurchaseUpdateRequest $request, Purchase $purchase)
    {
        $purchase->update($request->validated());

        return new PurchaseResource($purchase);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Purchase $purchase)
    {
        $purchase->delete();

        return response()->noContent();
    }
}
