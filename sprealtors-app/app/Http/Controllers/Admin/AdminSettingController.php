<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    /**
     * Editable settings grouped for the form, with their validation rules.
     *
     * @return array<string, array<string, array{label: string, rules: string, type?: string}>>
     */
    private function schema(): array
    {
        return [
            'General' => [
                'site_name' => ['label' => 'Site Name', 'rules' => 'required|string|max:120'],
                'tagline' => ['label' => 'Tagline', 'rules' => 'nullable|string|max:180'],
            ],
            'Contact' => [
                'phone' => ['label' => 'Phone', 'rules' => 'nullable|string|max:32'],
                'whatsapp' => ['label' => 'WhatsApp Number (digits with country code)', 'rules' => 'nullable|string|max:32'],
                'email' => ['label' => 'Email', 'rules' => 'nullable|email|max:180'],
                'address' => ['label' => 'Office Address', 'rules' => 'nullable|string|max:255'],
                'working_hours' => ['label' => 'Working Hours', 'rules' => 'nullable|string|max:120'],
                'map_url' => ['label' => 'Google Maps Embed URL', 'rules' => 'nullable|url|max:1000'],
            ],
            'Social Links' => [
                'facebook' => ['label' => 'Facebook URL', 'rules' => 'nullable|url|max:255'],
                'instagram' => ['label' => 'Instagram URL', 'rules' => 'nullable|url|max:255'],
                'linkedin' => ['label' => 'LinkedIn URL', 'rules' => 'nullable|url|max:255'],
                'youtube' => ['label' => 'YouTube URL', 'rules' => 'nullable|url|max:255'],
            ],
            'About Page Statistics' => [
                'stat_1_number' => ['label' => 'Stat 1 Number', 'rules' => 'nullable|string|max:40'],
                'stat_1_label' => ['label' => 'Stat 1 Label', 'rules' => 'nullable|string|max:60'],
                'stat_2_number' => ['label' => 'Stat 2 Number', 'rules' => 'nullable|string|max:40'],
                'stat_2_label' => ['label' => 'Stat 2 Label', 'rules' => 'nullable|string|max:60'],
                'stat_3_number' => ['label' => 'Stat 3 Number', 'rules' => 'nullable|string|max:40'],
                'stat_3_label' => ['label' => 'Stat 3 Label', 'rules' => 'nullable|string|max:60'],
                'stat_4_number' => ['label' => 'Stat 4 Number', 'rules' => 'nullable|string|max:40'],
                'stat_4_label' => ['label' => 'Stat 4 Label', 'rules' => 'nullable|string|max:60'],
            ],
            'Project Configuration Options' => [
                'config_unit_types' => [
                    'label' => 'Unit Types (one per line)',
                    'rules' => 'nullable|string|max:2000',
                    'type' => 'textarea',
                    'help' => 'Choices for the "Type" dropdown on project configurations — e.g. 2 BHK, Shop, Villa.',
                ],
                'config_area_types' => [
                    'label' => 'Area Types (one per line)',
                    'rules' => 'nullable|string|max:2000',
                    'type' => 'textarea',
                    'help' => 'Choices for the "Area Type" dropdown — e.g. Carpet Area, Built-up Area.',
                ],
            ],
            'SEO' => [
                'seo_title' => ['label' => 'Default SEO Title', 'rules' => 'nullable|string|max:180'],
                'seo_description' => ['label' => 'Default Meta Description', 'rules' => 'nullable|string|max:500', 'type' => 'textarea'],
            ],
        ];
    }

    public function edit(): View
    {
        return view('admin.settings', [
            'schema' => $this->schema(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];
        $groupFor = [];

        foreach ($this->schema() as $group => $fields) {
            foreach ($fields as $key => $config) {
                $rules[$key] = $config['rules'];
                $groupFor[$key] = strtolower(str_replace([' ', 'about page '], ['_', ''], $group));
            }
        }

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            Setting::put($key, $value, $groupFor[$key] ?? 'general');
        }

        Setting::flush();

        return back()->with('status', 'Settings saved successfully.');
    }
}
