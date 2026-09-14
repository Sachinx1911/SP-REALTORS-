<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $locations = Location::pluck('id', 'slug');

        $amenities = ['Club House', 'Swimming Pool', 'Gym', 'Kids Play Area', '24x7 Security', 'Landscaped Garden'];

        $projects = [
            [
                'name' => 'XYZ Residency',
                'location' => 'kharghar',
                'developer' => 'XYZ Developers',
                'starting_price' => 12500000,
                'configurations' => '2, 3 & 4 BHK Apartments',
                'possession' => 'Dec 2026',
                'rera_number' => 'P52000123456',
                'status' => 'under-construction',
                'is_featured' => true,
                'description' => 'Premium residential project offering thoughtfully designed 2, 3 and 4 BHK apartments in the heart of Kharghar, with modern amenities and excellent connectivity.',
                'highlights' => ['Prime Kharghar Location', 'Modern Club House', 'Landscaped Gardens', 'Vastu Compliant'],
                'configuration_details' => [
                    ['type' => '2 BHK', 'area' => '850 Sq.ft.', 'price' => '₹ 1.25 Cr*'],
                    ['type' => '3 BHK', 'area' => '1,150 Sq.ft.', 'price' => '₹ 1.75 Cr*'],
                    ['type' => '4 BHK', 'area' => '1,650 Sq.ft.', 'price' => '₹ 2.45 Cr*'],
                ],
                'nearby_places' => ['Kharghar Railway Station — 2 km', 'Central Park — 1.5 km', 'ISKCON Kharghar — 3 km', 'Schools & Hospitals — 1 km'],
            ],
            [
                'name' => 'Skyline Towers',
                'location' => 'panvel',
                'developer' => 'Skyline Group',
                'starting_price' => 7800000,
                'configurations' => '1 & 2 BHK Apartments',
                'possession' => 'Jun 2026',
                'rera_number' => 'P52000123457',
                'status' => 'under-construction',
                'is_featured' => true,
                'description' => 'Affordable yet premium 1 and 2 BHK homes in Panvel with quick access to the upcoming Navi Mumbai International Airport.',
                'highlights' => ['Airport Connectivity', 'Affordable Pricing', 'Gated Community'],
                'configuration_details' => [
                    ['type' => '1 BHK', 'area' => '620 Sq.ft.', 'price' => '₹ 78 Lac*'],
                    ['type' => '2 BHK', 'area' => '900 Sq.ft.', 'price' => '₹ 1.05 Cr*'],
                ],
                'nearby_places' => ['Panvel Railway Station — 3 km', 'Upcoming Airport — 8 km', 'Orion Mall — 2 km'],
            ],
            [
                'name' => 'Green View Heights',
                'location' => 'ulwe',
                'developer' => 'Green View Builders',
                'starting_price' => 9200000,
                'configurations' => '2 & 3 BHK Apartments',
                'possession' => 'Dec 2025',
                'rera_number' => 'P52000123458',
                'status' => 'ready-to-move',
                'description' => 'Ready-to-move 2 and 3 BHK apartments in Ulwe surrounded by open green spaces and excellent infrastructure.',
                'highlights' => ['Ready to Move', 'Green Surroundings', 'Spacious Balconies'],
                'configuration_details' => [
                    ['type' => '2 BHK', 'area' => '880 Sq.ft.', 'price' => '₹ 92 Lac*'],
                    ['type' => '3 BHK', 'area' => '1,200 Sq.ft.', 'price' => '₹ 1.35 Cr*'],
                ],
                'nearby_places' => ['Ulwe Railway Station — 1 km', 'Sea Woods Mall — 9 km'],
            ],
            [
                'name' => 'Oceanic Plaza',
                'location' => 'vashi',
                'developer' => 'Oceanic Infra',
                'starting_price' => 15000000,
                'configurations' => '3 & 4 BHK Apartments',
                'possession' => 'Mar 2027',
                'rera_number' => 'P52000123459',
                'status' => 'pre-launch',
                'description' => 'Landmark residential tower in Vashi offering premium 3 and 4 BHK homes with panoramic city views.',
                'highlights' => ['Landmark Tower', 'Panoramic Views', 'Premium Specifications'],
                'configuration_details' => [
                    ['type' => '3 BHK', 'area' => '1,250 Sq.ft.', 'price' => '₹ 1.50 Cr*'],
                    ['type' => '4 BHK', 'area' => '1,800 Sq.ft.', 'price' => '₹ 2.30 Cr*'],
                ],
                'nearby_places' => ['Vashi Railway Station — 1.5 km', 'Inorbit Mall — 2 km', 'Business District — 1 km'],
            ],
        ];

        foreach ($projects as $data) {
            $slug = str($data['name'])->slug()->value();
            $locationSlug = $data['location'];
            unset($data['location']);

            Project::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'location_id' => $locations[$locationSlug] ?? null,
                    'amenities' => $amenities,
                    'address' => ucwords(str_replace('-', ' ', $locationSlug)).', Navi Mumbai, Maharashtra',
                    'property_type' => 'residential',
                    'is_published' => true,
                ])
            );
        }
    }
}
