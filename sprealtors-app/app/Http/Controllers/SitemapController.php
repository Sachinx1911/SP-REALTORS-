<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Project;
use App\Models\Property;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        // Static pages.
        foreach ([
            ['home', '1.0', 'daily'],
            ['properties.index', '0.9', 'daily'],
            ['projects.index', '0.9', 'daily'],
            ['about', '0.6', 'monthly'],
            ['contact', '0.6', 'monthly'],
        ] as [$route, $priority, $frequency]) {
            $urls[] = [
                'loc' => route($route),
                'priority' => $priority,
                'changefreq' => $frequency,
            ];
        }

        // Properties.
        foreach (Property::published()->latest('updated_at')->get() as $property) {
            $urls[] = [
                'loc' => route('properties.show', $property),
                'lastmod' => $property->updated_at?->toAtomString(),
                'priority' => $property->is_featured ? '0.8' : '0.7',
                'changefreq' => 'weekly',
            ];
        }

        // Projects.
        foreach (Project::published()->latest('updated_at')->get() as $project) {
            $urls[] = [
                'loc' => route('projects.show', $project),
                'lastmod' => $project->updated_at?->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ];
        }

        // Location landing pages (the filtered property listing per area).
        foreach (Location::published()->ordered()->get() as $location) {
            $urls[] = [
                'loc' => route('properties.index', ['location' => [$location->slug]]),
                'priority' => '0.6',
                'changefreq' => 'weekly',
            ];
        }

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
