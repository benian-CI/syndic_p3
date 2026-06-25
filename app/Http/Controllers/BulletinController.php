<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    public function index(Request $request)
    {
        $villas = Villa::select(['id', 'street_id', 'number', 'owner_name'])
            ->with('street:id,name')
            ->orderBy('street_id')
            ->orderBy('number')
            ->get();

        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth();
        $selectedVilla = $request->get('villa_id') ? Villa::with('street')->find($request->get('villa_id')) : null;

        $contributions = collect();
        $total = 0;

        if ($selectedVilla) {
            $contributions = $selectedVilla->contributions()
                ->whereBetween('paid_at', [$start, $end])
                ->orderBy('paid_at')
                ->get();

            $total = $contributions->sum('amount');
        }

        if ($request->get('format') === 'stream' && $selectedVilla) {
            $fileName = sprintf('bulletin-villa-%s-%s-%s.pdf', $selectedVilla->number, $start->format('Ymd'), $end->format('Ymd'));
            return Pdf::loadView('bulletins.pdf', compact('selectedVilla', 'contributions', 'start', 'end', 'total'))
                ->stream($fileName);
        }

        if ($request->get('format') === 'pdf' && $selectedVilla) {
            $fileName = sprintf('bulletin-villa-%s-%s-%s.pdf', $selectedVilla->number, $start->format('Ymd'), $end->format('Ymd'));
            $pdf = Pdf::loadView('bulletins.pdf', compact('selectedVilla', 'contributions', 'start', 'end', 'total'));
            return $pdf->download($fileName);
        }

        return view('bulletins.index', compact('villas', 'selectedVilla', 'contributions', 'start', 'end', 'total'));
    }
}
