<?php

namespace Database\Seeders;

use App\Models\Rent;
use Illuminate\Database\Seeder;

class RentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rent::factory()->count(5)->create();
    }
}
