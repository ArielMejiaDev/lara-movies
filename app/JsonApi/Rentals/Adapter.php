<?php

namespace App\JsonApi\Rentals;

use App\Models\Rent;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Adapter extends AbstractAdapter
{
    /**
     * Mapping of JSON API attribute field names to model keys.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Mapping of JSON API filter names to model scopes.
     *
     * @var array
     */
    protected $filterScopes = [];

    protected $includePaths = [
        'users' => 'user',
        'movies' => 'movie'
    ];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new \App\Models\Rent(), $paging);
    }

    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        $this->filterWithScopes($query, $filters);
    }

    public function users()
    {
        return $this->belongsTo('user');
    }

    public function movies()
    {
        return $this->belongsTo('movie');
    }

    /**
     * @param Rent $rent
     * @param $resource
     * @return void
     */
    protected function creating(Rent $rent, $resource)
    {
        $rent->movie()->update([
            'stock' => ((int) $rent->movie->stock - 1)
        ]);
        $rent->user_id = auth()->user()->id;
    }

}
