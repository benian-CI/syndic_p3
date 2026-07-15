<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expenses.index', [
            'expenses' => Expense::when(request('q'), function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', '%' . $term . '%')
                        ->orWhere('category', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%');
                });
            })
                ->when(request('date_debut'), fn ($query, $date) => $query->whereDate('spent_at', '>=', $date))
                ->when(request('date_fin'), fn ($query, $date) => $query->whereDate('spent_at', '<=', $date))
                ->latest('spent_at')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        Expense::create($this->validated($request));
        DashboardController::clearCache();

        return redirect()->route('expenses.index')->with('success', 'Depense ajoutee.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $expense->update($this->validated($request));
        DashboardController::clearCache();

        return redirect()->route('expenses.index')->with('success', 'Depense modifiee.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        DashboardController::clearCache();

        return redirect()->route('expenses.index')->with('success', 'Depense supprimee.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent_at' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
