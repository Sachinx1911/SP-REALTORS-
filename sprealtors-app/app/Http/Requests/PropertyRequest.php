<?php

namespace App\Http\Requests;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest
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
        $propertyId = $this->route('property')?->id;

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash', Rule::unique('properties', 'slug')->ignore($propertyId)],
            'location_id' => ['nullable', 'exists:locations,id'],
            'property_type' => ['required', Rule::in(['residential', 'commercial'])],
            'purpose' => ['required', Rule::in(['buy', 'rent'])],
            'configuration' => ['nullable', Rule::in(array_keys(Property::configurationOptions()))],
            'status' => ['required', Rule::in(array_keys(Property::statusOptions()))],
            'furnishing' => ['nullable', Rule::in(array_keys(Property::furnishingOptions()))],

            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999999'],
            'price_negotiable' => ['boolean'],
            'is_monthly' => ['boolean'],

            'area' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'area_unit' => ['nullable', 'string', 'max:16'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'car_parking' => ['nullable', 'integer', 'min:0', 'max:50'],

            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'highlights' => ['nullable', 'string', 'max:2000'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:60'],

            'rera_number' => ['nullable', 'string', 'max:80'],
            'possession' => ['nullable', 'string', 'max:80'],
            'map_url' => ['nullable', 'url', 'max:1000'],

            'contact_phone' => ['nullable', 'string', 'max:32'],
            'contact_whatsapp' => ['nullable', 'string', 'max:32'],

            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:500'],

            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],

            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:20'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'price_negotiable' => $this->boolean('price_negotiable'),
            'is_monthly' => $this->boolean('is_monthly'),
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
        ]);
    }
}
