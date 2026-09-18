<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('website');
        $data['ip_address'] = $request->ip();
        $data['status'] = 'new';

        $enquiry = Enquiry::create($data);
        $enquiry->notifyOwner();

        return back()
            ->with('enquiry_success', 'Thank you! Your enquiry has been received. We will get back to you shortly.')
            ->withFragment('enquiry');
    }
}
