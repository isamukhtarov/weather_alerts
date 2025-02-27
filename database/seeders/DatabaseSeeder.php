<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();

        User::query()->create([
            'name' => 'Isa',
            'email' => 'isa.m@solid-sl.com',
            'password' => bcrypt('qwerty'),
            'phone' => '+994706638946',
            'email_verified_at' => now(),
        ]);

    }
}
