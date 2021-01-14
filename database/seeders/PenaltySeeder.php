<?php

namespace Database\Seeders;

use App\Models\Penalty;
use Illuminate\Database\Seeder;

class PenaltySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Penalty::factory()->count(5)->create();
    }
}
