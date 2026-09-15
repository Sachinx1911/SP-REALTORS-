<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BrochureController extends Controller
{
    /**
     * Capture the visitor's details, then unlock the download for this session.
     */
    public function store(StoreEnquiryRequest $request, Project $project): RedirectResponse
    {
        abort_unless($project->is_published && $project->hasBrochure(), 404);

        $data = $request->safe()->except('website');
        $data['project_id'] = $project->id;
        $data['source'] = 'brochure';
        $data['status'] = 'new';
        $data['ip_address'] = $request->ip();
        $data['message'] = $data['message']
            ?? 'Requested the brochure for '.$project->name.'.';

        Enquiry::create($data);

        $request->session()->put($project->brochureUnlockKey(), true);

        // Go back to the project page so the visitor sees a confirmation, and
        // hand it the URL to pull the file from. Redirecting straight at the
        // download would leave them on a page with no feedback — and the
        // lead popup would then ask for the same details all over again.
        return redirect()
            ->route('projects.show', $project)
            ->with('enquiry_success', 'Thank you! Your brochure download is starting.')
            ->with('brochure_download_url', route('projects.brochure.download', $project));
    }

    /**
     * Stream the brochure. Only reachable once details have been submitted.
     */
    public function download(Request $request, Project $project): StreamedResponse
    {
        abort_unless($project->hasBrochure(), 404);

        // Panel users can always fetch it (to check what they uploaded);
        // visitors must have submitted the lead form first.
        $isStaff = $request->user()?->hasAccess() ?? false;

        if (! $isStaff) {
            abort_unless($project->is_published, 404);
            abort_unless($request->session()->get($project->brochureUnlockKey()), 403);
        }

        $disk = Storage::disk('local');
        abort_unless($disk->exists($project->brochure), 404);

        $filename = str($project->name)->slug()->value().'-brochure.pdf';

        return $disk->download($project->brochure, $filename);
    }
}
