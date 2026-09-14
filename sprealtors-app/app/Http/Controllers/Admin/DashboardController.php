<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Location;
use App\Models\Project;
use App\Models\Property;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Properties', 'value' => Property::count(), 'icon' => 'home', 'route' => route('admin.properties.index')],
                ['label' => 'Projects', 'value' => Project::count(), 'icon' => 'building', 'route' => route('admin.projects.index')],
                ['label' => 'Locations', 'value' => Location::count(), 'icon' => 'map-pin', 'route' => route('admin.locations.index')],
                ['label' => 'Testimonials', 'value' => Testimonial::count(), 'icon' => 'quote', 'route' => route('admin.testimonials.index')],
            ],
            'newEnquiries' => Enquiry::where('status', 'new')->count(),
            'totalEnquiries' => Enquiry::count(),
            'recentEnquiries' => Enquiry::with(['property', 'project'])->latest()->take(8)->get(),
        ]);
    }
}
