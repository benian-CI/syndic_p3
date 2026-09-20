<?php

namespace App\Http\Controllers;

use App\Models\PartnerSyndic;
use Illuminate\Http\Request;

class PartnerSyndicController extends Controller
{
    public function index()
    {
        return view('partner-syndics.index', [
            'partnerSyndics' => PartnerSyndic::when(request('q'), fn ($query, $term) => $query->where('name', 'like', '%' . $term . '%'))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('partner-syndics.create');
    }

    public function store(Request $request)
    {
        PartnerSyndic::create($this->validated($request));

        return redirect()->route('partner-syndics.index')->with('success', 'Syndic partenaire ajoute.');
    }

    public function edit(PartnerSyndic $partnerSyndic)
    {
        return view('partner-syndics.edit', compact('partnerSyndic'));
    }

    public function update(Request $request, PartnerSyndic $partnerSyndic)
    {
        $partnerSyndic->update($this->validated($request));

        return redirect()->route('partner-syndics.index')->with('success', 'Syndic partenaire modifie.');
    }

    public function destroy(PartnerSyndic $partnerSyndic)
    {
        $partnerSyndic->delete();

        return redirect()->route('partner-syndics.index')->with('success', 'Syndic partenaire supprime.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'login_url' => ['nullable', 'url', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:actif,inactif'],
        ]);
    }
}
