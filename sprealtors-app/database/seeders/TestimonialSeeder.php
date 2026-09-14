<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'client_name' => 'Amit Patil',
                'role' => '2 BHK Buyer',
                'property_type' => 'Residential',
                'review' => 'Very professional and helpful. They guided us through the entire process and helped us find the right home in Kharghar.',
            ],
            [
                'client_name' => 'Sneha More',
                'role' => 'Commercial Tenant',
                'property_type' => 'Commercial',
                'review' => 'Great support and transparent dealing. Highly recommended for anyone looking for property in Navi Mumbai.',
            ],
            [
                'client_name' => 'Rajesh Kulkarni',
                'role' => 'Investor',
                'property_type' => 'Residential',
                'review' => 'Helped me get a good deal and handled all the documentation smoothly. Excellent service throughout.',
            ],
        ];

        foreach ($testimonials as $index => $data) {
            Testimonial::updateOrCreate(
                ['client_name' => $data['client_name']],
                array_merge($data, [
                    'rating' => 5,
                    'sort_order' => $index,
                    'is_published' => true,
                ])
            );
        }
    }
}
