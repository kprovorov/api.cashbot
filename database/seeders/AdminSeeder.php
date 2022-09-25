<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::forceCreate([
            'name' => 'Kirill Provorov',
            'email' => 'kirill@provorov.dev',
            'password' => Hash::make('secret'),
        ]);
    }
}
