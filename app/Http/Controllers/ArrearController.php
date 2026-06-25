<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArrearController extends Controller
{
    public function index(Request $request)
    {
        $villas = Villa::select(['id', 'street_id', 'number', 'owner_name'])
            ->with('street:id,name')
            ->orderBy('street_id')
            ->orderBy('number')
            ->get();

        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfMonth() : Carbon::now()->startOfMonth();
        $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfMonth() : Carbon::now()->endOfMonth();
        $selectedVilla = $request->get('villa_id') ? Villa::with(['street', 'contributions' => function ($query) use ($start, $end) {
            $query->whereBetween('month', [$start, $end])->orderBy('month');
        }])->find($request->get('villa_id')) : null;

        $arrears = collect();

        if ($selectedVilla) {
            $paidMonths = $selectedVilla->contributions->pluck('month')->map(function ($month) {
                return Carbon::parse($month)->format('Y-m');
            })->all();

            $current = $start->copy();
            while ($current->lte($end)) {
                $key = $current->format('Y-m');
                if (! in_array($key, $paidMonths, true)) {
                    $arrears->push(['month' => $current->copy()]);
                }
                $current->addMonth();
            }
        }

        if ($request->get('format') === 'stream' && $selectedVilla) {
            $fileName = sprintf('arrires-villa-%s-%s-%s.pdf', $selectedVilla->number, $start->format('Ymd'), $end->format('Ymd'));
            return Pdf::loadView('arrears.pdf', compact('selectedVilla', 'arrears', 'start', 'end'))
                ->stream($fileName);
        }

        if ($request->get('format') === 'pdf' && $selectedVilla) {
            $fileName = sprintf('arrires-villa-%s-%s-%s.pdf', $selectedVilla->number, $start->format('Ymd'), $end->format('Ymd'));
            $pdf = Pdf::loadView('arrears.pdf', compact('selectedVilla', 'arrears', 'start', 'end'));
            return $pdf->download($fileName);
        }

        return view('arrears.index', compact('villas', 'selectedVilla', 'arrears', 'start', 'end'));
    }
}
