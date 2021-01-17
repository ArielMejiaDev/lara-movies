<?php

namespace App\Rules;

use App\Models\Movie;
use Illuminate\Contracts\Validation\Rule;

class ExistsMoviesInStock implements Rule
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
        if($movie = Movie::find($value)) {
            return $movie->stock !== 0;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'There is no stock for this movie.';
    }
}
