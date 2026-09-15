<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Location;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Support\Amenities;
use App\Support\ImageUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->with('location')
            ->when($request->string('q')->value(), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => new Project(['is_published' => true, 'status' => 'under-construction']),
            'locations' => Location::ordered()->get(),
            'amenityOptions' => Amenities::options(),
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = Project::create($this->payload($request));

        $this->syncImages($request, $project);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('status', 'Project created successfully.');
    }

    public function show(Project $project): RedirectResponse
    {
        return redirect()->route('admin.projects.edit', $project);
    }

    public function edit(Project $project): View
    {
        $project->load('images');

        return view('admin.projects.form', [
            'project' => $project,
            'locations' => Location::ordered()->get(),
            'amenityOptions' => Amenities::options(),
        ]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($this->payload($request, $project));

        $this->syncImages($request, $project);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('status', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        ImageUploader::delete($project->hero_image);

        foreach ($project->images as $image) {
            ImageUploader::delete($image->path);
        }

        if ($project->brochure) {
            Storage::disk('local')->delete($project->brochure);
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('status', 'Project deleted.');
    }

    public function destroyBrochure(Project $project): RedirectResponse
    {
        if ($project->brochure) {
            Storage::disk('local')->delete($project->brochure);
            $project->update(['brochure' => null]);
        }

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('status', 'Brochure removed.');
    }

    public function destroyImage(ProjectImage $image): RedirectResponse
    {
        $projectId = $image->project_id;
        ImageUploader::delete($image->path);
        $image->delete();

        return redirect()
            ->route('admin.projects.edit', $projectId)
            ->with('status', 'Image removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(ProjectRequest $request, ?Project $project = null): array
    {
        $data = $request->safe()->except([
            'hero_image', 'brochure', 'gallery', 'floor_plans',
            'highlights', 'nearby_places',
            'config_type', 'config_area', 'config_area_type', 'config_price', 'config_all_inclusive',
        ]);

        $data['highlights'] = $this->lines($request->input('highlights'));
        $data['nearby_places'] = $this->lines($request->input('nearby_places'));
        $data['amenities'] = Amenities::mergeCustom(
            (array) $request->input('amenities', []),
            $request->input('custom_amenities')
        ) ?: null;

        // Configuration rows: parallel arrays, one entry per row in the form.
        $types = (array) $request->input('config_type', []);
        $areas = (array) $request->input('config_area', []);
        $areaTypes = (array) $request->input('config_area_type', []);
        $prices = (array) $request->input('config_price', []);
        $allInclusive = (array) $request->input('config_all_inclusive', []);

        $configs = [];
        foreach ($types as $i => $type) {
            if (blank($type)) {
                continue;
            }
            $configs[] = array_filter([
                'type' => trim($type),
                'area' => trim((string) ($areas[$i] ?? '')),
                'area_type' => trim((string) ($areaTypes[$i] ?? '')),
                'price' => trim((string) ($prices[$i] ?? '')),
                // Checkbox value carries the row index so it survives gaps.
                'all_inclusive' => in_array((string) $i, array_map('strval', $allInclusive), true),
            ], fn ($value) => $value !== '' && $value !== false && $value !== null);
        }
        $data['configuration_details'] = $configs ?: null;

        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = ImageUploader::replace(
                $request->file('hero_image'),
                'projects',
                $project?->hero_image
            );
        }

        // Brochures live on the private disk so they can only be reached
        // through BrochureController, after the lead form is submitted.
        if ($request->hasFile('brochure')) {
            if ($project?->brochure) {
                Storage::disk('local')->delete($project->brochure);
            }
            $data['brochure'] = $request->file('brochure')->store('brochures', 'local');
        }

        return $data;
    }

    private function syncImages(ProjectRequest $request, Project $project): void
    {
        foreach (['gallery' => 'gallery', 'floor_plans' => 'floor_plan'] as $field => $type) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $nextOrder = (int) $project->images()->where('type', $type)->max('sort_order');

            foreach ($request->file($field) as $file) {
                $project->images()->create([
                    'path' => ImageUploader::store($file, 'projects'),
                    'alt' => $project->name,
                    'type' => $type,
                    'sort_order' => ++$nextOrder,
                ]);
            }
        }
    }

    /**
     * Split a textarea into a clean list, one item per line.
     *
     * @return list<string>|null
     */
    private function lines(?string $value): ?array
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return $lines ?: null;
    }
}
