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
            'likes' => (int) $resource->likes,
//            'createdAt' => $resource->created_at,
//            'updatedAt' => $resource->updated_at,
        ];
    }
}
