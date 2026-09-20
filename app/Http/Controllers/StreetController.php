<?php

namespace App\Http\Controllers;

use App\Models\Street;
use App\Support\Quartier;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        Street::create($this->validated($request));

        return redirect()->route('streets.index')->with('success', 'Rue ajoutee.');
    }

    public function edit(Street $street)
    {
        return view('streets.edit', compact('street'));
    }

    public function update(Request $request, Street $street)
    {
        $street->update($this->validated($request));

        return redirect()->route('streets.index')->with('success', 'Rue modifiee.');
    }

    public function destroy(Street $street)
    {
        $street->delete();

        return redirect()->route('streets.index')->with('success', 'Rue supprimee.');
    }

    /** Une rue ne peut etre placee que dans le quartier (rayon modifiable sur la page Carte). */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if (isset($data['latitude'], $data['longitude']) && ! Quartier::contains((float) $data['latitude'], (float) $data['longitude'])) {
            throw ValidationException::withMessages([
                'latitude' => 'La rue doit etre situee dans le quartier ' . Quartier::name() . ' (rayon de ' . Quartier::radiusKm() . ' km).',
            ]);
        }

        return $data;
    }
}
