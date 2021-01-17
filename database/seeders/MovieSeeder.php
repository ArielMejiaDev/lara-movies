<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $movies = Movie::factory()->hasPurchases(3)->count(25)->create();

        $movies->each(function ($movie) {
            $movie->likes()->create(['user_id' => User::inRandomOrder()->first()->id]);
        });
    }
}
