<?php

namespace App\JsonApi\Rentals;

use Carbon\Carbon;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'rentals';

    /**
     * @param \App\Models\Rent $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\Models\Rent $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'user_id' => $resource->user_id,
            'movie_id' => $resource->movie_id,
            'days_of_rent' => $resource->days_of_rent,
            'day_to_return_movie' => $resource->date_to_return_movie->toISOString(),
            'penalty' => $resource->penalty,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }

    public function getRelationships($rent, $isPrimary, array $includeRelationships)
    {
        return [
            'users' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['users']),
                self::DATA => function () use ($rent) {
                    return $rent->user;
                },
            ],
            'movies' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['movies']),
                self::DATA => function () use ($rent) {
                    return $rent->movie;
                },
            ],
        ];
    }
}
