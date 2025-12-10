<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mentora.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'avg_rating' => 0,
                'is_suspended' => false,
            ]
        );

        $defaultCategories = ['Penulisan', 'Desain', 'Pemrograman', 'Penelitian'];

        foreach ($defaultCategories as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
        }
    }
}
#commit