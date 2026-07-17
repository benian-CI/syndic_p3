<?php

namespace App\Http\Controllers;

use App\Models\Street;

class MapController extends Controller
{
    public function index()
    {
        return view('map.index', [
            'streets' => Street::withCount('villas')->orderBy('name')->get(),
        ]);
    }
}
