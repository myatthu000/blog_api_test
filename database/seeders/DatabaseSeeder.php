<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

        User::query()->updateOrCreate([
            'name' => 'admin1',
            'uuid' => fake()->uuid(),
            'email' => 'admin1@gmail.com',
            'role' => 'user',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        User::query()->updateOrCreate([
            'name' => 'admin2',
            'uuid' => fake()->uuid(),
            'email' => 'admin2@gmail.com',
            'role' => 'user',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        User::query()->updateOrCreate([
            'name' => 'admin3',
            'uuid' => fake()->uuid(),
            'email' => 'admin3@gmail.com',
            'role' => 'user',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $this->call([
            CategorySeeder::class,
            PostSeeder::class,
        ]);
//        $path = asset('public/');
//        Storage::deleteDirectory($path);
//        dd('done',$path);
    }
}
