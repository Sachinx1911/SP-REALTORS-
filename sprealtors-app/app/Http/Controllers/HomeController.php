<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Project;
use App\Models\Property;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Property::query()
            ->published()
            ->featured()
            ->with('location')
            ->latest()
            ->take(3)
            ->get();

        // Fall back to the newest listings so the section is never empty.
        if ($featured->isEmpty()) {
            $featured = Property::query()
                ->published()
                ->with('location')
                ->latest()
                ->take(3)
                ->get();
        }

        $featuredProjects = Project::query()
            ->published()
            ->featured()
            ->with('location')
            ->latest()
            ->take(3)
            ->get();

        if ($featuredProjects->isEmpty()) {
            $featuredProjects = Project::query()
                ->published()
                ->with('location')
                ->latest()
                ->take(3)
                ->get();
        }

        return view('pages.home', [
            'featured' => $featured,
            'featuredProjects' => $featuredProjects,
            'locations' => Location::published()->ordered()->take(8)->get(),
            'testimonials' => Testimonial::published()->ordered()->take(3)->get(),
            'filterLocations' => Location::published()->ordered()->get(),
        ]);
    }
}
