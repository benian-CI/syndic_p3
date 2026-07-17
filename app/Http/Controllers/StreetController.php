<?php

namespace App\Http\Controllers;

use App\Models\Street;
use Illuminate\Http\Request;

class StreetController extends Controller
{
    public function index()
    {    
        return view('streets.index', [
            'streets' => Street::withCount('villas')
                ->when(request('q'), fn ($query, $term) => $query->where('name', 'like', '%' . $term . '%'))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('streets.create');
    }

    public function store(Request $request)
    {
        Street::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('streets.index')->with('success', 'Rue ajoutee.');
    }

    public function edit(Street $street)
    {
        return view('streets.edit', compact('street'));
    }

    public function update(Request $request, Street $street)
    {
        $street->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('streets.index')->with('success', 'Rue modifiee.');
    }

    public function destroy(Street $street)
    {
        $street->delete();

        return redirect()->route('streets.index')->with('success', 'Rue supprimee.');
    }
}
