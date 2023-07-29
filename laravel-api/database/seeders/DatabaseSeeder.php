<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        \App\Models\Role::create([
            'name' => 'Administrator',
            'description' => 'super user',
        ]);

        \App\Models\Role::create([
            'name' => 'User',
            'description' => 'can add expenses',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juandelacruz@email.com',
            'password' => 'secret',
            'role_id' => '1',
        ]);

        \App\Models\ExpenseCategory::create([
            'name' => 'Travel',
            'description' => 'daily commute'
        ]);

        \App\Models\ExpenseCategory::create([
            'name' => 'Entertainment',
            'description' => 'movies etc'
        ]);
    }
}
