<?php

namespace App\Rules;

use App\Models\Like;
use Illuminate\Contracts\Validation\Rule;

class ExistsALikeBefore implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Like::where('user_id', request()->data['attributes']['user_id'])
            ->where('movie_id', request()->data['attributes']['movie_id'])
            ->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The user already liked the movie.';
    }
}
