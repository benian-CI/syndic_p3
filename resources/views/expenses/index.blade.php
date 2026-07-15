<x-layouts.app title="Dépenses">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Dépenses</h1>
            <p class="muted">Suivi des sorties d'argent du quartier.</p>
        </div>
        <div class="actions">
            <x-export-buttons resource="expenses" />
            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                <a class="btn" href="{{ route('expenses.create') }}" data-turbo-frame="modal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Ajouter une dépense
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('expenses.index') }}" class="filter-bar search-filter">
        <label>Recherche
            <input name="q" value="{{ request('q') }}" placeholder="Titre, catégorie, description">
        </label>
        <label>Date dépense début
            <input type="date" name="date_debut" value="{{ request('date_debut') }}">
        </label>
        <label>Date fin
            <input type="date" name="date_fin" value="{{ request('date_fin') }}">
        </label>
        <div class="filter-actions">
            <button class="btn" type="submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Rechercher
            </button>
        </div>
    </form>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Dépense</th>
                        <th>Catégorie</th>
                        <th>Montant</th>
                        <th>Créée le</th>
                        @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                            <th><span class="sr-only">Actions</span></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td class="muted" style="white-space:nowrap">{{ $expense->spent_at->format('d/m/Y') }}</td>
                            <td>
                                <div style="font-weight:var(--weight-medium)">{{ $expense->title }}</div>
                                @if($expense->description)<div class="muted">{{ $expense->description }}</div>@endif
                            </td>
                            <td>
                                @if($expense->category)
                                    <span class="badge badge-neutral">{{ $expense->category }}</span>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                            <td style="font-weight:var(--weight-semibold)">{{ number_format($expense->amount, 0, ',', ' ') }}</td>
                            <td class="muted">{{ $expense->created_at->format('d/m/Y') }}</td>
                            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                                <td class="actions">
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('expenses.edit', $expense) }}" data-turbo-frame="modal" aria-label="Modifier">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <form class="inline confirm-delete" method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                            @csrf @method('DELETE')
                                            <button class="icon-btn danger" type="submit" aria-label="Supprimer">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="{{ in_array(auth()->user()->role, ['admin', 'gestionnaire'], true) ? 6 : 5 }}">
                                <div class="empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="2" y="6" width="20" height="12" rx="2" ry="2"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        <line x1="6" y1="12" x2="6.01" y2="12"/>
                                        <line x1="18" y1="12" x2="18.01" y2="12"/>
                                    </svg>
                                    <p>Aucune dépense pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination-wrap">{{ $expenses->links() }}</div>
</x-layouts.app>
