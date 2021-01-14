<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $stock
 * @property string $rental_price
 * @property string $sale_price
 * @property bool $availability
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Movie extends Model
{
    use HasFactory, Sortifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'stock',
        'rental_price',
        'sale_price',
        'availability',
        'likes',
    ];

    protected $allowedSortFields = ['title', 'likes'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'availability' => 'boolean',
    ];
}
