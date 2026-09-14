<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $locations = Location::pluck('id', 'slug');

        $amenities = ['Lift', 'Security', 'Power Backup', 'Car Parking', 'Gym', "Children's Play Area", 'Garden'];

        $properties = [
            [
                'title' => '2 BHK Apartment in Sector 20, Kharghar',
                'location' => 'kharghar',
                'property_type' => 'residential',
                'purpose' => 'buy',
                'configuration' => '2bhk',
                'status' => 'ready-to-move',
                'furnishing' => 'unfurnished',
                'price' => 8500000,
                'price_negotiable' => true,
                'area' => 850,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'car_parking' => 1,
                'is_featured' => true,
                'description' => 'Spacious 2 BHK apartment available for sale in Kharghar, Navi Mumbai. The property offers a comfortable layout, excellent ventilation and is located in a well-connected area with easy access to schools, hospitals, markets and public transport. Ideal for families looking for a modern home in a prime location.',
                'highlights' => ['Prime Location', 'Good Connectivity', 'Spacious Layout', 'Peaceful Surroundings', 'Nearby Schools & Hospitals'],
                'rera_number' => 'P52000012345',
            ],
            [
                'title' => '1 BHK Apartment in Panvel',
                'location' => 'panvel',
                'property_type' => 'residential',
                'purpose' => 'buy',
                'configuration' => '1bhk',
                'status' => 'ready-to-move',
                'furnishing' => 'semi-furnished',
                'price' => 5200000,
                'area' => 650,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'car_parking' => 1,
                'is_featured' => true,
                'description' => 'Well-maintained 1 BHK apartment in Panvel with good connectivity to the railway station and upcoming Navi Mumbai International Airport.',
                'highlights' => ['Near Railway Station', 'Airport Connectivity', 'Ready to Move'],
            ],
            [
                'title' => 'Commercial Office Space in Vashi',
                'location' => 'vashi',
                'property_type' => 'commercial',
                'purpose' => 'buy',
                'configuration' => 'office',
                'status' => 'ready-to-move',
                'furnishing' => 'furnished',
                'price' => 12000000,
                'area' => 1200,
                'car_parking' => 2,
                'is_featured' => true,
                'description' => 'Fully furnished commercial office space in a prime Vashi business district location, ideal for corporate offices and startups.',
                'highlights' => ['Business District', 'Fully Furnished', 'Ample Parking'],
            ],
            [
                'title' => '3 BHK Premium Apartment in Ulwe',
                'location' => 'ulwe',
                'property_type' => 'residential',
                'purpose' => 'buy',
                'configuration' => '3bhk',
                'status' => 'under-construction',
                'furnishing' => 'unfurnished',
                'price' => 11000000,
                'area' => 1100,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'car_parking' => 1,
                'description' => 'Premium 3 BHK apartment in a gated community at Ulwe with modern amenities and excellent airport connectivity.',
                'highlights' => ['Gated Community', 'Modern Amenities', 'Airport Connectivity'],
            ],
            [
                'title' => 'Shop for Rent in Taloja',
                'location' => 'taloja',
                'property_type' => 'commercial',
                'purpose' => 'rent',
                'configuration' => 'shop',
                'status' => 'ready-to-move',
                'furnishing' => 'unfurnished',
                'price' => 55000,
                'is_monthly' => true,
                'area' => 450,
                'description' => 'Ground floor shop available on rent in a high-footfall market area of Taloja.',
                'highlights' => ['High Footfall', 'Main Road Facing', 'Ground Floor'],
            ],
            [
                'title' => 'Residential Plot in Panvel',
                'location' => 'panvel',
                'property_type' => 'residential',
                'purpose' => 'buy',
                'configuration' => 'plot',
                'status' => 'ready-to-move',
                'price' => 4800000,
                'area' => 1100,
                'description' => 'Clear-title residential plot in a developing area of Panvel, suitable for building an independent home.',
                'highlights' => ['Clear Title', 'Developing Area', 'Good Investment'],
            ],
            [
                'title' => '2 BHK Flat for Rent in Kamothe',
                'location' => 'kamothe',
                'property_type' => 'residential',
                'purpose' => 'rent',
                'configuration' => '2bhk',
                'status' => 'ready-to-move',
                'furnishing' => 'semi-furnished',
                'price' => 18000,
                'is_monthly' => true,
                'area' => 900,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'car_parking' => 1,
                'description' => 'Semi-furnished 2 BHK flat available on rent in Kamothe, close to schools and daily-needs markets.',
                'highlights' => ['Family Building', 'Near Schools', 'Semi-Furnished'],
            ],
            [
                'title' => '4 BHK Independent Villa in Nerul',
                'location' => 'nerul',
                'property_type' => 'residential',
                'purpose' => 'buy',
                'configuration' => '4bhk+',
                'status' => 'ready-to-move',
                'furnishing' => 'furnished',
                'price' => 32000000,
                'area' => 2400,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'car_parking' => 2,
                'description' => 'Luxurious independent villa in a premium Nerul neighbourhood with private garden and covered parking.',
                'highlights' => ['Independent Villa', 'Private Garden', 'Premium Locality'],
            ],
            [
                'title' => '1 BHK Flat in CBD Belapur',
                'location' => 'cbd-belapur',
                'property_type' => 'residential',
                'purpose' => 'rent',
                'configuration' => '1bhk',
                'status' => 'ready-to-move',
                'furnishing' => 'furnished',
                'price' => 22000,
                'is_monthly' => true,
                'area' => 600,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'description' => 'Fully furnished 1 BHK flat on rent in CBD Belapur, walking distance from the railway station.',
                'highlights' => ['Walk to Station', 'Fully Furnished', 'Business District'],
            ],
        ];

        foreach ($properties as $data) {
            $slug = str($data['title'])->slug()->value();
            $locationSlug = $data['location'];
            unset($data['location']);

            Property::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'location_id' => $locations[$locationSlug] ?? null,
                    'amenities' => $amenities,
                    'address' => ucwords(str_replace('-', ' ', $locationSlug)).', Navi Mumbai, Maharashtra',
                    'is_published' => true,
                    'area_unit' => 'Sq.ft.',
                ])
            );
        }
    }
}
