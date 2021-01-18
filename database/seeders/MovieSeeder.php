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
        $moviesData = collect([
            [
                'title' => 'Iron Man',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/81wW59VEXpL._SL1500_.jpg',
            ],
            [
                'title' => 'The Incredible Hulk',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/61ikONHVOAL._AC_SY741_.jpg',
            ],
            [
                'title' => 'Iron Man 2',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91UgoL1eiUL._SL1500_.jpg',
            ],
            [
                'title' => 'Thor',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91P1wWqX63L._AC_SL1500_.jpg',
            ],
            [
                'title' => 'Captain America: The First Avenger',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/71pXkYJfZaL._AC_SY741_.jpg',
            ],
            [
                'title' => 'Marvel’s The Avengers',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/71xZtfOsHdL._AC_SY741_.jpg',
            ],
            [
                'title' => 'Iron Man 3',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/71xZtfOsHdL._AC_SY741_.jpg',
            ],
            [
                'title' => 'Thor: The Dark World',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/711dbTn6RXL._AC_SY741_.jpg',
            ],
            [
                'title' => 'Captain America: The Winter Soldier',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91OxromzoSL._AC_SL1500_.jpg',
            ],
            [
                'title' => 'Guardians of the Galaxy',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/617dF1MDxIL._AC_SY679_.jpg',
            ],
            [
                'title' => 'Avengers: Age of Ultron',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/71wV2rzkFwL._AC_SL1022_.jpg',
            ],
            [
                'title' => 'Ant-Man',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91jKEijKMYL._SL1500_.jpg',
            ],
            [
                'title' => 'Captain America: Civil War',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91GBtXiURaL._AC_SL1500_.jpg',
            ],
            [
                'title' => 'Doctor Strange',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/A12-NFRep6L._AC_SL1500_.jpg',
            ],
            [
                'title' => 'Guardians of the Galaxy Vol. 2',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/81Zqwo1ovGL._SL1500_.jpg',
            ],
            [
                'title' => 'Spider-Man: Homecoming',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/A1CcbJfKqJL._AC_SY679_.jpg',
            ],
            [
                'title' => 'Thor: Ragnarok',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91l5WO1j5ML.jpg',
            ],
            [
                'title' => 'Black Panther',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91eR7HRz9TL.jpg',
            ],
            [
                'title' => 'Avengers: Infinity War',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/A1t8xCe9jwL._AC_SL1500_.jpg',
            ],
            [
                'title' => 'Ant-Man and the Wasp',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/A1icPty19AL.jpg',
            ],
            [
                'title' => 'Captain Marvel',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91VwaglLKpL._AC_SY679_.jpg',
            ],
            [
                'title' => 'Avengers: Endgame',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/81ai6zx6eXL._AC_SL1304_.jpg',
            ],
            [
                'title' => 'Spider-Man: Far From Home',
                'image' => 'https://images-na.ssl-images-amazon.com/images/I/91A+eXyGmvL._RI_.jpg',
            ],
        ]);

        $moviesData->each(function($movie) {
            Movie::factory()->hasPurchases(1)->hasLikes(10)->create([
                'title' => $movie['title'],
                'image' => $movie['image'],
                'stock' => 3,
            ]);
        });

//        $movies = Movie::factory()->hasPurchases(3)->count(25)->create();
//
//        $movies->each(function ($movie) {
//            $movie->likes()->create(['user_id' => User::inRandomOrder()->first()->id]);
//        });
    }
}
