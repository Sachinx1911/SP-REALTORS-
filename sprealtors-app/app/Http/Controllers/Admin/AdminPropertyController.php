<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Support\Amenities;
use App\Support\ImageUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminPropertyController extends Controller
{
    public function index(Request $request): View
    {
        $properties = Property::query()
            ->with('location')
            ->when($request->string('q')->value(), fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->when($request->string('status')->value(), function ($q, $status) {
                $q->where('is_published', $status === 'published');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.properties.index', compact('properties'));
    }

    public function create(): View
    {
        return view('admin.properties.form', [
            'property' => new Property(['is_published' => true, 'area_unit' => 'Sq.ft.', 'status' => 'ready-to-move']),
            'locations' => Location::ordered()->get(),
            'amenityOptions' => Amenities::options(),
        ]);
    }

    public function store(PropertyRequest $request): RedirectResponse
    {
        $property = Property::create($this->payload($request));

        $this->syncImages($request, $property);

        return redirect()
            ->route('admin.properties.edit', $property)
            ->with('status', 'Property created successfully.');
    }

    public function show(Property $property): RedirectResponse
    {
        return redirect()->route('admin.properties.edit', $property);
    }

    public function edit(Property $property): View
    {
        $property->load('images');

        return view('admin.properties.form', [
            'property' => $property,
            'locations' => Location::ordered()->get(),
            'amenityOptions' => Amenities::options(),
        ]);
    }

    public function update(PropertyRequest $request, Property $property): RedirectResponse
    {
        $property->update($this->payload($request, $property));

        $this->syncImages($request, $property);

        return redirect()
            ->route('admin.properties.edit', $property)
            ->with('status', 'Property updated successfully.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        ImageUploader::delete($property->main_image);

        foreach ($property->images as $image) {
            ImageUploader::delete($image->path);
        }

        $property->delete();

        return redirect()
            ->route('admin.properties.index')
            ->with('status', 'Property deleted.');
    }

    public function destroyImage(PropertyImage $image): RedirectResponse
    {
        $propertyId = $image->property_id;
        ImageUploader::delete($image->path);
        $image->delete();

        return redirect()
            ->route('admin.properties.edit', $propertyId)
            ->with('status', 'Image removed.');
    }

    /**
     * Map the validated request onto the model columns.
     *
     * @return array<string, mixed>
     */
    private function payload(PropertyRequest $request, ?Property $property = null): array
    {
        $data = $request->safe()->except(['main_image', 'gallery', 'highlights']);

        // Highlights come in as one per line.
        $data['highlights'] = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('highlights')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all() ?: null;

        $data['amenities'] = $request->input('amenities') ?: null;

        if ($request->hasFile('main_image')) {
            $data['main_image'] = ImageUploader::replace(
                $request->file('main_image'),
                'properties',
                $property?->main_image
            );
        }

        return $data;
    }

    private function syncImages(PropertyRequest $request, Property $property): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $nextOrder = (int) $property->images()->max('sort_order');

        foreach ($request->file('gallery') as $file) {
            $property->images()->create([
                'path' => ImageUploader::store($file, 'properties'),
                'alt' => $property->title,
                'sort_order' => ++$nextOrder,
            ]);
        }
    }
}
