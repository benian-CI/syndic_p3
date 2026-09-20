<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\PartnerSyndic;
use App\Models\Setting;
use App\Models\Street;
use App\Support\Quartier;
use App\Models\Villa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['month' => ['nullable', 'date_format:Y-m']]);

        // Les donnees de paiement ne sont calculees ni envoyees aux profils non financiers.
        $showFinance = in_array($request->user()?->role, ['admin', 'gestionnaire'], true);

        $streets = Street::withCount('villas')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name');

        $month = null;
        $summary = null;

        if ($showFinance) {
            $month = $this->resolveMonth($request->query('month'));
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $streets->withCount(['villas as paid_villas_count' => fn ($query) => $query->whereHas(
                'contributions',
                fn ($contribution) => $contribution->whereBetween('month', [$start, $end])
            )]);

            $collected = Contribution::whereBetween('month', [$start, $end])
                ->join('villas', 'villas.id', '=', 'contributions.villa_id')
                ->groupBy('villas.street_id')
                ->selectRaw('villas.street_id, SUM(contributions.amount) as total')
                ->pluck('total', 'street_id');

            $streets = $streets->get()->each(fn ($street) => $street->collected = (float) ($collected[$street->id] ?? 0));

            $totalVillas = Villa::count();
            $paidVillas = Villa::whereHas('contributions', fn ($query) => $query->whereBetween('month', [$start, $end]))->count();

            $summary = [
                'totalVillas' => $totalVillas,
                'paidVillas' => $paidVillas,
                'rate' => $totalVillas ? round($paidVillas / $totalVillas * 100) : 0,
                'collected' => (float) $collected->sum(),
            ];
        } else {
            $streets = $streets->get();
        }

        return view('map.index', [
            'streets' => $streets,
            'partnerSyndics' => PartnerSyndic::where('status', 'actif')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('name')
                ->get(),
            'showFinance' => $showFinance,
            'month' => $month,
            'summary' => $summary,
        ]);
    }

    /** Rayon du quartier (km) : les nouvelles rues doivent se trouver dans ce rayon autour du centre. */
    public function updateRadius(Request $request)
    {
        $data = $request->validate([
            'radius_km' => ['required', 'numeric', 'between:0.1,5'],
        ]);

        Setting::set(Quartier::RADIUS_SETTING, (float) $data['radius_km']);

        $outside = Street::whereNotNull('latitude')->whereNotNull('longitude')->get()
            ->reject(fn ($street) => Quartier::contains((float) $street->latitude, (float) $street->longitude))
            ->count();

        $message = 'Rayon du quartier fixe a ' . (float) $data['radius_km'] . ' km.';
        if ($outside > 0) {
            $message .= ' Attention : ' . $outside . ' rue(s) existante(s) se trouvent maintenant hors de la zone.';
        }

        return redirect()->route('map.index', $request->only('month'))->with('success', $message);
    }

    /** Mois demande, sinon le dernier mois ayant des cotisations, sinon le mois courant. */
    private function resolveMonth(?string $requested): Carbon
    {
        if ($requested) {
            return Carbon::createFromFormat('Y-m-d', $requested . '-01')->startOfMonth();
        }

        $latest = Contribution::max('month');

        return $latest ? Carbon::parse($latest)->startOfMonth() : Carbon::now()->startOfMonth();
    }
}
