<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Movie
 *
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchase[] $purchases
 * @property-read int|null $purchases_count
 * @method static Builder|Movie availability($value)
 * @method static Builder|Movie newModelQuery()
 * @method static Builder|Movie newQuery()
 * @method static Builder|Movie query()
 * @method static Builder|Movie title($value)
 * @method static Builder|Movie whereAvailability($value)
 * @method static Builder|Movie whereCreatedAt($value)
 * @method static Builder|Movie whereDescription($value)
 * @method static Builder|Movie whereId($value)
 * @method static Builder|Movie whereImage($value)
 * @method static Builder|Movie whereRentalPrice($value)
 * @method static Builder|Movie whereSalePrice($value)
 * @method static Builder|Movie whereStock($value)
 * @method static Builder|Movie whereTitle($value)
 * @method static Builder|Movie whereUpdatedAt($value)
 * @mixin \Eloquent
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
        'stock' => 'integer',
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

    public function getRentalPriceAttribute()
    {
        return number_format($this->attributes['rental_price'] / 100, 2);
    }

    public function setRentalPriceAttribute($value)
    {
        $this->attributes['rental_price'] = $value * 100;
    }

    public function getSalePriceAttribute()
    {
        return number_format($this->attributes['sale_price'] / 100, 2);
    }

    public function setSalePriceAttribute($value)
    {
        $this->attributes['sale_price'] = $value * 100;
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
