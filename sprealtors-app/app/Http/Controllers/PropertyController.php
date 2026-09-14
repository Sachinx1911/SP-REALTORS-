<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'purpose' => $request->string('purpose')->value() ?: null,
            'type' => array_filter((array) $request->input('type', [])),
            'configuration' => array_filter((array) $request->input('configuration', [])),
            'location' => array_filter((array) $request->input('location', [])),
            'budget' => array_filter((array) $request->input('budget', [])),
            'search' => $request->string('q')->value() ?: null,
        ];

        $properties = Property::query()
            ->published()
            ->with('location')
            ->filter($filters)
            ->sorted($request->string('sort')->value())
            ->paginate(9)
            ->withQueryString();

        return view('pages.properties.index', [
            'properties' => $properties,
            'filters' => $filters,
            'sort' => $request->string('sort')->value(),
            'locations' => Location::published()->ordered()->withCount(['properties' => fn ($q) => $q->where('is_published', true)])->get(),
            'typeCounts' => $this->counts('property_type'),
            'purposeCounts' => $this->counts('purpose'),
            'configurationCounts' => $this->counts('configuration'),
            'budgetCounts' => $this->budgetCounts(),
        ]);
    }

    public function show(Property $property): View
    {
        abort_unless($property->is_published, 404);

        $property->load(['location', 'images']);

        $related = Property::query()
            ->published()
            ->where('id', '!=', $property->id)
            ->where(function ($q) use ($property) {
                $q->where('location_id', $property->location_id)
                    ->orWhere('property_type', $property->property_type);
            })
            ->with('location')
            ->latest()
            ->take(4)
            ->get();

        return view('pages.properties.show', compact('property', 'related'));
    }

    /**
     * Published-property counts grouped by a column, for the filter sidebar.
     *
     * @return array<string, int>
     */
    private function counts(string $column): array
    {
        return Property::query()
            ->published()
            ->whereNotNull($column)
            ->selectRaw("{$column} as value, COUNT(*) as total")
            ->groupBy($column)
            ->pluck('total', 'value')
            ->all();
    }

    /**
     * Published-property counts per budget bucket.
     *
     * @return array<string, int>
     */
    private function budgetCounts(): array
    {
        $counts = [];

        foreach (array_keys(Property::budgetOptions()) as $key) {
            [$min, $max] = Property::budgetRange($key);

            $counts[$key] = Property::query()
                ->published()
                ->when($min !== null, fn ($q) => $q->where('price', '>=', $min))
                ->when($max !== null, fn ($q) => $q->where('price', '<', $max))
                ->count();
        }

        return $counts;
    }
}
