<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->admin()->create([
            'name' => 'Ariel',
            'email' => 'arielmejiadev@gmail.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->times(10)->guest()->create();
    }
}
