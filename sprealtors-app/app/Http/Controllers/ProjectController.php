<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->published()
            ->with('location')
            ->when($request->string('location')->value(), function ($q, $slug) {
                $q->whereHas('location', fn ($l) => $l->where('slug', $slug));
            })
            ->when($request->string('status')->value(), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('pages.projects.index', [
            'projects' => $projects,
            'locations' => Location::published()->ordered()->get(),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        $project->load(['location', 'images']);

        $related = Project::query()
            ->published()
            ->where('id', '!=', $project->id)
            ->with('location')
            ->latest()
            ->take(3)
            ->get();

        return view('pages.projects.show', compact('project', 'related'));
    }
}
