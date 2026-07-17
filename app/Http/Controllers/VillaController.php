<?php

namespace App\Http\Controllers;

use App\Models\Street;
use App\Models\Villa;
use Illuminate\Http\Request;

class VillaController extends Controller
{
    public function index()
    {
        return view('villas.index', [
            'villas' => Villa::with('street')
                ->withSum('contributions', 'amount')
                ->when(request('q'), function ($query, $term) {
                    $query->where(function ($query) use ($term) {
                        $query->where('number', 'like', '%' . $term . '%')
                            ->orWhere('owner_name', 'like', '%' . $term . '%')
                            ->orWhere('owner_email', 'like', '%' . $term . '%')
                            ->orWhere('owner_phone', 'like', '%' . $term . '%')
                            ->orWhereHas('street', fn ($streetQuery) => $streetQuery->where('name', 'like', '%' . $term . '%'));
                    });
                })
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('villas.create', ['streets' => $this->streets()]);
    }

    public function store(Request $request)
    {
        Villa::create($this->validated($request));

        return redirect()->route('villas.index')->with('success', 'Villa ajoutee.');
    }

    public function edit(Villa $villa)
    {
        return view('villas.edit', [
            'villa' => $villa,
            'streets' => $this->streets(),
        ]);
    }

    public function update(Request $request, Villa $villa)
    {
        $villa->update($this->validated($request));

        return redirect()->route('villas.index')->with('success', 'Villa modifiee.');
    }

    public function destroy(Villa $villa)
    {
        $villa->delete();

        return redirect()->route('villas.index')->with('success', 'Villa supprimee.');
    }

    private function streets()
    {
        return Street::orderBy('name')->get(['id', 'name']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'street_id' => ['required', 'exists:streets,id'],
            'number' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['nullable', 'email', 'max:255'],
            'owner_phone' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
    }
}
