<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch the most recent approved properties with their first image
        $properties = \App\Models\Property::with(['images', 'user'])
            ->where('status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('properties'));
    }
}
