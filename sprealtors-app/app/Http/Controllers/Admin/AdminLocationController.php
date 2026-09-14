<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Support\ImageUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminLocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::query()
            ->withCount(['properties', 'projects'])
            ->ordered()
            ->paginate(20);

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.form', [
            'location' => new Location(['is_published' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = ImageUploader::store($request->file('image'), 'locations');
        }

        Location::create($data);

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Location created successfully.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.form', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $data = $this->validated($request, $location);

        if ($request->hasFile('image')) {
            $data['image'] = ImageUploader::replace($request->file('image'), 'locations', $location->image);
        }

        $location->update($data);

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Location updated successfully.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        ImageUploader::delete($location->image);
        $location->delete();

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Location deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Location $location = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', 'alpha_dash', Rule::unique('locations', 'slug')->ignore($location?->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['image']);

        return $data;
    }
}
