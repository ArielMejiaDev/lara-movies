<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'articles',
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => [
                'title' => $this->resource->title,
                'description' => $this->resource->description,
                'image' => $this->resource->image,
                'stock' => $this->resource->stock,
                'rental_price' => $this->resource->rental_price,
                'sale_price' => $this->resource->sale_price,
                'availability' => $this->resource->availability,
                'likes' => (int) $this->resource->likes,
            ],
            'links' => [
                'self' => route('api:v1:movies.show', $this->resource)
            ],
        ];
    }
}
