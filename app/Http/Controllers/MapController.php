<?php

namespace App\Http\Controllers;

use App\Models\Villa;

class MapController extends Controller
{
    public function index()
    {
        return view('map.index', [
            'villas' => Villa::with('street')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('number')
                ->get(),
        ]);
    }
}
