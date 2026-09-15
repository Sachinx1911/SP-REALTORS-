<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAccess() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return [
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($projectId)],
            'developer' => ['nullable', 'string', 'max:180'],
            'location_id' => ['nullable', 'exists:locations,id'],

            'starting_price' => ['nullable', 'numeric', 'min:0', 'max:99999999999'],
            'configurations' => ['nullable', 'string', 'max:180'],
            'possession' => ['nullable', 'string', 'max:80'],
            'rera_number' => ['nullable', 'string', 'max:80'],
            'property_type' => ['required', Rule::in(['residential', 'commercial'])],
            'status' => ['required', Rule::in(array_keys(Project::statusOptions()))],

            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'highlights' => ['nullable', 'string', 'max:2000'],
            'nearby_places' => ['nullable', 'string', 'max:2000'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:60'],
            'custom_amenities' => ['nullable', 'string', 'max:1000'],

            'config_type' => ['nullable', 'array'],
            'config_type.*' => ['nullable', 'string', 'max:40'],
            'config_area' => ['nullable', 'array'],
            'config_area.*' => ['nullable', 'string', 'max:40'],
            'config_price' => ['nullable', 'array'],
            'config_price.*' => ['nullable', 'string', 'max:40'],

            'map_url' => ['nullable', 'url', 'max:1000'],
            'contact_phone' => ['nullable', 'string', 'max:32'],
            'contact_whatsapp' => ['nullable', 'string', 'max:32'],

            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:500'],

            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],

            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'gallery' => ['nullable', 'array', 'max:20'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'floor_plans' => ['nullable', 'array', 'max:20'],
            'floor_plans.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
        ]);
    }
}
