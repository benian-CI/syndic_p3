<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Villa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $contributions = Contribution::query()
            ->when($request->date_debut, fn ($query, $date) => $query->whereDate('month', '>=', $date))
            ->when($request->date_fin, fn ($query, $date) => $query->whereDate('month', '<=', $date));

        $events = Event::query()
            ->when($request->date_debut, fn ($query, $date) => $query->whereDate('month', '>=', $date))
            ->when($request->date_fin, fn ($query, $date) => $query->whereDate('month', '<=', $date));

        $expenses = Expense::query()
            ->when($request->date_debut, fn ($query, $date) => $query->whereDate('spent_at', '>=', $date))
            ->when($request->date_fin, fn ($query, $date) => $query->whereDate('spent_at', '<=', $date));

        $filterKey = md5(($request->date_debut ?? '') . '|' . ($request->date_fin ?? ''));

        $filteredStats = Cache::remember("dashboard_filtered_{$filterKey}", 300, function () use ($contributions, $events, $expenses) {
            return [
                'totalExpenses' => (float) (clone $expenses)->sum('amount'),
                'totalContributions' => (float) (clone $contributions)->sum('amount') + (float) (clone $events)->sum('amount'),
                'contributingVillas' => (clone $contributions)->distinct('villa_id')->count('villa_id'),
            ];
        });

        $absoluteStats = Cache::remember('dashboard_absolute', 300, function () use ($currentMonth) {
            $now = Carbon::now();
            $today = Carbon::today();

            return [
                'totalVillas' => Villa::count(),
                'totalOwners' => Villa::distinct('owner_name')->count('owner_name'),
                'monthlyContributions' => (float) Contribution::whereDate('month', $currentMonth)->sum('amount')
                    + (float) Event::whereDate('month', $currentMonth)->sum('amount'),
                'monthlyContributingVillas' => Contribution::whereDate('month', $currentMonth)->distinct('villa_id')->count('villa_id'),
                'expensesToday' => (float) Expense::whereDate('spent_at', $today)->sum('amount'),
                'expensesThisMonth' => (float) Expense::whereMonth('spent_at', $now->month)->whereYear('spent_at', $now->year)->sum('amount'),
                'expensesThisYear' => (float) Expense::whereYear('spent_at', $now->year)->sum('amount'),
                'contributionsToday' => (float) Contribution::whereDate('paid_at', $today)->sum('amount')
                    + (float) Event::whereDate('paid_at', $today)->sum('amount'),
                'contributionsThisMonth' => (float) Contribution::whereMonth('paid_at', $now->month)->whereYear('paid_at', $now->year)->sum('amount')
                    + (float) Event::whereMonth('paid_at', $now->month)->whereYear('paid_at', $now->year)->sum('amount'),
                'contributionsThisYear' => (float) Contribution::whereYear('paid_at', $now->year)->sum('amount')
                    + (float) Event::whereYear('paid_at', $now->year)->sum('amount'),
            ];
        });

        return view('dashboard', array_merge($filteredStats, $absoluteStats, [
            'recentContributions' => $this->recentPayments($request),
            'recentExpenses' => (clone $expenses)->latest('spent_at')->take(6)->get(),
        ]));
    }

    public static function clearCache(?string $dateDebut = null, ?string $dateFin = null): void
    {
        Cache::forget('dashboard_absolute');
        Cache::forget('dashboard_filtered_' . md5(($dateDebut ?? '') . '|' . ($dateFin ?? '')));
    }

    private function recentPayments(Request $request): Collection
    {
        $contributions = Contribution::with('villa')
            ->when($request->date_debut, fn ($query, $date) => $query->whereDate('month', '>=', $date))
            ->when($request->date_fin, fn ($query, $date) => $query->whereDate('month', '<=', $date))
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Contribution $contribution) => [
                'primary' => 'Villa ' . $contribution->villa->number,
                'secondary' => $contribution->villa->owner_name,
                'month' => $contribution->month ?? $contribution->paid_at,
                'amount' => $contribution->amount,
                'at' => $contribution->created_at,
            ]);

        $events = Event::query()
            ->when($request->date_debut, fn ($query, $date) => $query->whereDate('month', '>=', $date))
            ->when($request->date_fin, fn ($query, $date) => $query->whereDate('month', '<=', $date))
            ->latest('paid_at')
            ->take(6)
            ->get()
            ->map(fn (Event $event) => [
                'primary' => $event->title,
                'secondary' => 'Ressource additionnelle',
                'month' => $event->month ?? $event->paid_at,
                'amount' => $event->amount,
                'at' => $event->paid_at ?? $event->created_at,
            ]);

        return $contributions->concat($events)
            ->sortByDesc('at')
            ->take(6)
            ->values();
    }
}
