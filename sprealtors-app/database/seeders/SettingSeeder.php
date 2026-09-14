<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'contact' => ['phone', 'whatsapp', 'email', 'address', 'working_hours', 'map_url'],
            'social' => ['facebook', 'instagram', 'linkedin', 'youtube'],
            'stats' => [
                'stat_1_number', 'stat_1_label', 'stat_2_number', 'stat_2_label',
                'stat_3_number', 'stat_3_label', 'stat_4_number', 'stat_4_label',
            ],
            'seo' => ['seo_title', 'seo_description'],
        ];

        $groupFor = function (string $key) use ($groups): string {
            foreach ($groups as $group => $keys) {
                if (in_array($key, $keys, true)) {
                    return $group;
                }
            }

            return 'general';
        };

        foreach (Setting::defaults() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $groupFor($key)]
            );
        }

        Setting::flush();
    }
}
