<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MovieStoreRequest;
use App\Http\Requests\Api\MovieUpdateRequest;
use App\Http\Resources\Api\MovieCollection;
use App\Http\Resources\Api\MovieResource;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Api\MovieCollection
     */
    public function index(Request $request)
    {
        $movies = Movie::applyApiFilter()->applyApiSort()->jsonPaginate();

        return new MovieCollection($movies);
    }

    /**
     * @param \App\Http\Requests\Api\MovieStoreRequest $request
     * @return \App\Http\Resources\Api\MovieResource
     */
    public function store(MovieStoreRequest $request)
    {
        $movie = Movie::create($request->validated());

        return new MovieResource($movie);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Movie $movie
     * @return \App\Http\Resources\Api\MovieResource
     */
    public function show(Request $request, Movie $movie)
    {
        return new MovieResource($movie);
    }

    /**
     * @param \App\Http\Requests\Api\MovieUpdateRequest $request
     * @param \App\Models\Movie $movie
     * @return \App\Http\Resources\Api\MovieResource
     */
    public function update(MovieUpdateRequest $request, Movie $movie)
    {
        $movie->update($request->validated());

        return new MovieResource($movie);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Movie $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Movie $movie)
    {
        $movie->delete();

        return response()->noContent();
    }
}
