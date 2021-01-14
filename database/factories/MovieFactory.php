<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Movie;

class MovieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text,
            'image' => $this->faker->word,
            'stock' => $this->faker->word,
            'rental_price' => $this->faker->word,
            'sale_price' => $this->faker->word,
            'availability' => $this->faker->boolean,
            'likes' => $this->faker->numberBetween(20, 100),
        ];
    }
}
