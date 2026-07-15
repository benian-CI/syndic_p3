<?php
namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContributionController extends Controller
{
    public function index()
    {
        return view('contributions.index', [
            'contributions' => Contribution::with('villa.street')
                ->when(request('q'), function ($query, $term) {
                    $query->where(function ($query) use ($term) {
                        $query->where('payment_method', 'like', '%' . $term . '%')
                            ->orWhere('reference', 'like', '%' . $term . '%')
                            ->orWhere('notes', 'like', '%' . $term . '%')
                            ->orWhereHas('villa', function ($villaQuery) use ($term) {
                                $villaQuery->where('number', 'like', '%' . $term . '%')
                                    ->orWhere('owner_name', 'like', '%' . $term . '%')
                                    ->orWhere('owner_email', 'like', '%' . $term . '%')
                                    ->orWhere('owner_phone', 'like', '%' . $term . '%')
                                    ->orWhereHas('street', fn ($streetQuery) => $streetQuery->where('name', 'like', '%' . $term . '%'));
                            });
                    });
                })
                ->when(request('mois'), fn ($query, $mois) => $query->whereDate('month', $mois . '-01'))
                ->when(request('paiement_debut'), fn ($query, $date) => $query->whereDate('paid_at', '>=', $date))
                ->when(request('paiement_fin'), fn ($query, $date) => $query->whereDate('paid_at', '<=', $date))
                ->latest('month')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('contributions.create', ['villas' => $this->villas()]);
    }

    public function store(Request $request)
    {
        Contribution::create($this->validated($request));
        DashboardController::clearCache();

        return redirect()->route('contributions.index')->with('success', 'Cotisation enregistree.');
    }

    public function edit(Contribution $contribution)
    {
        return view('contributions.edit', [
            'contribution' => $contribution,
            'villas' => $this->villas(),
        ]);
    }

    public function update(Request $request, Contribution $contribution)
    {
        $contribution->update($this->validated($request, $contribution));
        DashboardController::clearCache();

        return redirect()->route('contributions.index')->with('success', 'Cotisation modifiee.');
    }

    public function destroy(Contribution $contribution)
    {
        $contribution->delete();
        DashboardController::clearCache();

        return redirect()->route('contributions.index')->with('success', 'Cotisation supprimee.');
    }

    private function validated(Request $request, ?Contribution $contribution = null): array
    {
        $data = $request->validate([
            'villa_id' => ['required', 'exists:villas,id'],
            'month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['month'] .= '-01';

        $exists = Contribution::where('villa_id', $data['villa_id'])
            ->whereDate('month', $data['month'])
            ->when($contribution, fn ($query) => $query->whereKeyNot($contribution->id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'month' => 'Cette villa a deja une cotisation pour ce mois.',
            ]);
        }

        return $data;
    }

    private function villas()
    {
        return Villa::select(['id', 'street_id', 'number', 'owner_name'])
            ->with('street:id,name')
            ->orderBy('number')
            ->get();
    }
}
