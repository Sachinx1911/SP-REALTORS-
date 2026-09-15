<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@sprealtors.in'],
            [
                'name' => 'SP Realtors Admin',
                'password' => 'password',
            ]
        );

        // Set outside the fillable payload — see User::$fillable.
        $admin->forceFill(['is_admin' => true])->save();

        $this->call([
            SettingSeeder::class,
            LocationSeeder::class,
            PropertySeeder::class,
            ProjectSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
