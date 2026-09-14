<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sprealtors.in'],
            [
                'name' => 'SP Realtors Admin',
                'password' => 'password',
                'is_admin' => true,
            ]
        );

        $this->call([
            SettingSeeder::class,
            LocationSeeder::class,
            PropertySeeder::class,
            ProjectSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
