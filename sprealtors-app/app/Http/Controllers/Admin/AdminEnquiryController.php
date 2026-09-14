<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminEnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $enquiries = Enquiry::query()
            ->with(['property', 'project'])
            ->status($request->string('status')->value())
            ->source($request->string('source')->value())
            ->when($request->string('q')->value(), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'statusCounts' => Enquiry::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all(),
        ]);
    }

    public function show(Enquiry $enquiry): View
    {
        $enquiry->load(['property', 'project']);

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Enquiry::statusOptions()))],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $enquiry->update($data);

        return back()->with('status', 'Enquiry updated.');
    }

    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()
            ->route('admin.enquiries.index')
            ->with('status', 'Enquiry deleted.');
    }
}
