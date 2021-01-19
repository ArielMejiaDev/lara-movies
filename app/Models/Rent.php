<?php

namespace App\Models;

use App\Actions\PenaltyCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rent
 *
 * @property int $id
 * @property int $movie_id
 * @property int $user_id
 * @property \Carbon\Carbon $rental_limit_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Rent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rent query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rent whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rent whereRentalLimitAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rent whereUserId($value)
 * @mixin \Eloquent
 */
class Rent extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'movie_id',
        'user_id',
        'days_of_rent',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'movie_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * @return Carbon
     */
    public function getDateToReturnMovieAttribute()
    {
        return Carbon::createFromTimeString($this->attributes['created_at'])->addDays($this->attributes['days_of_rent']);
    }

    /**
     * @return float|int|string
     */
    public function getPenaltyAttribute()
    {
        return (new PenaltyCalculator)->calculateFromDate($this->getDateToReturnMovieAttribute());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function movie()
    {
        return $this->belongsTo(\App\Models\Movie::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
