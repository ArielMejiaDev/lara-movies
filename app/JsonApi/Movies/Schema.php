<?php

namespace App\JsonApi\Movies;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'movies';

    /**
     * @param \App\Models\Movie $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\Models\Movie $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'title' => $resource->title,
            'description' => $resource->description,
            'image' => $resource->image,
            'stock' => $resource->stock,
            'rental_price' => $resource->rental_price,
            'sale_price' => $resource->sale_price,
            'availability' => $resource->availability,
            'likes_counter' => $resource->likes()->count(),
            'liked_by_user' => $resource->liked(),
        ];
    }

    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        return [
            'likes' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['likes']),
                self::DATA => function() use($resource) {
                    return $resource->likes;
                }
            ]
        ];
    }
}
