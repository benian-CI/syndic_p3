<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function index()
    {
        return view('events.index', [
            'events' => Event::query()
                ->when(request('q'), function ($query, $term) {
                    $query->where(function ($query) use ($term) {
                        $query->where('title', 'like', '%' . $term . '%')
                            ->orWhere('description', 'like', '%' . $term . '%')
                            ->orWhere('payment_method', 'like', '%' . $term . '%')
                            ->orWhere('reference', 'like', '%' . $term . '%');
                    });
                })
                ->when(request('date_debut'), fn ($query, $date) => $query->whereDate('paid_at', '>=', $date))
                ->when(request('date_fin'), fn ($query, $date) => $query->whereDate('paid_at', '<=', $date))
                ->latest('paid_at')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        Event::create($this->validated($request));
        DashboardController::clearCache();

        return redirect()->route('events.index')->with('success', 'Ressource Additionnelle  ajoutée.');
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $event->update($this->validated($request));
        DashboardController::clearCache();

        return redirect()->route('events.index')->with('success', 'Ressource Additionnelle modifie.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        DashboardController::clearCache();

        return redirect()->route('events.index')->with('success', 'Ressource Additionnelle supprime.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data['month'] .= '-01';

        return $data;
    }
}
