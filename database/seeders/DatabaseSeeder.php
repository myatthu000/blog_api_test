<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         User::factory(10)->create();

         User::query()->updateOrCreate([
             'name' => 'admin',
             'uuid' => fake()->uuid(),
             'email' => 'admin@gmail.com',
             'role' => 'admin',
             'password' => Hash::make('password'),
             'email_verified_at' => now(),
             'remember_token' => Str::random(10),
         ]);

        $this->call([
            CategorySeeder::class,
            PostSeeder::class,
        ]);
    }
}
