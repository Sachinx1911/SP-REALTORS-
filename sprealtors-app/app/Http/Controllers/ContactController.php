<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact', [
            'faqs' => [
                [
                    'question' => 'What types of properties do you deal with?',
                    'answer' => 'We deal in residential and commercial properties including apartments, villas, plots, offices and shops across Navi Mumbai.',
                ],
                [
                    'question' => 'Do you also deal in rental properties?',
                    'answer' => 'Yes. We assist with both buying/selling and rentals, whether you are looking to rent a home or lease out your property.',
                ],
                [
                    'question' => 'Do you charge any brokerage?',
                    'answer' => 'Our brokerage terms are transparent and competitive. Please contact us for details specific to your transaction.',
                ],
                [
                    'question' => 'Which areas in Navi Mumbai do you cover?',
                    'answer' => 'We cover Kharghar, Panvel, Kamothe, Ulwe, Taloja, Vashi, Nerul, CBD Belapur and surrounding nodes.',
                ],
                [
                    'question' => 'Can you help with home loan assistance?',
                    'answer' => 'Yes. We have tie-ups with leading banks and can assist with home loan processing and documentation.',
                ],
                [
                    'question' => 'How can I schedule a site visit?',
                    'answer' => 'Call us, send a WhatsApp message, or fill in the enquiry form on this page and we will arrange a visit at your convenience.',
                ],
            ],
        ]);
    }
}
