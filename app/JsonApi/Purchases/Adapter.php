<?php

namespace App\JsonApi\Purchases;

use App\Models\Movie;
use App\Models\Purchase;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        parent::__construct(new \App\Models\Purchase(), $paging);
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

    protected function creating(Purchase $purchase): void
    {
        $purchase->movie()->update([
            'stock' => ((int) $purchase->movie->stock - 1)
        ]);
        $purchase->user_id = request()->user('api')->getRouteKey();
    }
}
