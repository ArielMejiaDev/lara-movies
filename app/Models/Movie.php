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
    use HasFactory;

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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'availability' => 'boolean',
    ];

    public function scopeTitle(Builder $query, $value)
    {
        $query->where('title', 'LIKE', "%{$value}%");
    }

    public function scopeAvailability(Builder $query, $value)
    {
        $query->where('availability', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function liked()
    {
        return $this->likes()->where('user_id', optional(request()->user('api'))->id)->count();
    }
}
