<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Kharghar', 'Panvel', 'Kamothe', 'Ulwe',
            'Taloja', 'Vashi', 'Nerul', 'CBD Belapur',
        ];

        foreach ($locations as $index => $name) {
            Location::updateOrCreate(
                ['slug' => str($name)->slug()->value()],
                [
                    'name' => $name,
                    'description' => "Explore residential and commercial properties in {$name}, Navi Mumbai.",
                    'seo_title' => "Properties in {$name}, Navi Mumbai | SP REALTORS",
                    'seo_description' => "Browse verified flats, offices and plots for sale and rent in {$name}, Navi Mumbai.",
                    'sort_order' => $index,
                    'is_published' => true,
                ]
            );
        }
    }
}
