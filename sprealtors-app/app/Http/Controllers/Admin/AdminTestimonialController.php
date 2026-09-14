<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\ImageUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminTestimonialController extends Controller
{
    public function index(): View
    {
        $testimonials = Testimonial::ordered()->paginate(20);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        return view('admin.testimonials.form', [
            'testimonial' => new Testimonial(['is_published' => true, 'rating' => 5]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageUploader::store($request->file('photo'), 'testimonials');
        }

        Testimonial::create($data);

        return redirect()
            ->route('admin.testimonials.index')
            ->with('status', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageUploader::replace($request->file('photo'), 'testimonials', $testimonial->photo);
        }

        $testimonial->update($data);

        return redirect()
            ->route('admin.testimonials.index')
            ->with('status', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        ImageUploader::delete($testimonial->photo);
        $testimonial->delete();

        return redirect()
            ->route('admin.testimonials.index')
            ->with('status', 'Testimonial deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:120'],
            'review' => ['required', 'string', 'max:2000'],
            'role' => ['nullable', 'string', 'max:120'],
            'property_type' => ['nullable', 'string', 'max:120'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['rating'] = $data['rating'] ?? 5;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['photo']);

        return $data;
    }
}
