<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AboutController extends Controller
{
    /**
     * Display the about page.
     */
    public function index()
    {
        return Inertia::render('About/Index', [
            'aboutImage' => Setting::get('about.image', ''),
            'aboutText' => Setting::get('about.text', ''),
        ]);
    }
}
