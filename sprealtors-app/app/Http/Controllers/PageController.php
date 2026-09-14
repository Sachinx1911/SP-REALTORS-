<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $stats = [];

        for ($i = 1; $i <= 4; $i++) {
            $number = setting("stat_{$i}_number");
            if (filled($number)) {
                $stats[] = [
                    'number' => $number,
                    'label' => setting("stat_{$i}_label"),
                ];
            }
        }

        return view('pages.about', compact('stats'));
    }
}
