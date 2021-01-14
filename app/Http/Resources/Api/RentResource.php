<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RentResource extends JsonResource
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
            'id' => $this->id,
            'movie_id' => $this->movie_id,
            'user_id' => $this->user_id,
            'rental_limit_at' => $this->rental_limit_at,
        ];
    }
}
