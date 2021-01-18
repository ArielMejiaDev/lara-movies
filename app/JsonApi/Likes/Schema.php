<?php

namespace App\JsonApi\Likes;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'likes';

    /**
     * @param \App\Models\Like $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\Models\Like $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }

    public function getRelationships($like, $isPrimary, array $includeRelationships)
    {
        return [
            'users' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['users']),
                self::DATA => function () use ($like) {
                    return $like->user;
                },
            ],
            'movies' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['movies']),
                self::DATA => function () use ($like) {
                    return $like->movie;
                },
            ],
        ];
    }
}
