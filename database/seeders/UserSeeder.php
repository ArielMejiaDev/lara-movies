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
            'email_verified_at' => null,
        ]);

        User::factory()->admin()->create([
            'email' => 'admin@mail.com',
            'email_verified_at' => null,
        ]);

        User::factory()->guest()->create([
            'email' => 'guest@mail.com',
            'email_verified_at' => null,
        ]);

        User::factory()->times(10)->guest()->create();
    }
}
