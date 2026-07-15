<x-layouts.app title="Cotisations">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Cotisations mensuelles</h1>
            <p class="muted">Chaque villa cotise une fois par mois.</p>
        </div>
        <div class="actions">
            <x-export-buttons resource="contributions" />
            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                <a class="btn" href="{{ route('contributions.create') }}" data-turbo-frame="modal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Ajouter une cotisation
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('contributions.index') }}" class="filter-bar contributions-filter">
        <label>Recherche
            <input name="q" value="{{ request('q') }}" placeholder="Villa, propriétaire, rue, méthode, référence">
        </label>
        <label>Mois
            <input type="month" name="mois" value="{{ request('mois') }}">
        </label>
        <label>Paiement du
            <input type="date" name="paiement_debut" value="{{ request('paiement_debut') }}">
        </label>
        <label>au
            <input type="date" name="paiement_fin" value="{{ request('paiement_fin') }}">
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
                        <th>Mois</th>
                        <th>Villa</th>
                        <th>Propriétaire</th>
                        <th>Date paiement</th>
                        <th>Montant</th>
                        <th>Créé le</th>
                        @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                            <th><span class="sr-only">Actions</span></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contributions as $contribution)
                        <tr>
                            <td><span class="badge badge-neutral">{{ $contribution->month->format('m/Y') }}</span></td>
                            <td style="font-weight:var(--weight-medium)">
                                {{ $contribution->villa->number }}
                                <span class="muted">— {{ optional($contribution->villa->street)->name ?: '—' }}</span>
                            </td>
                            <td>{{ $contribution->villa->owner_name }}</td>
                            <td class="muted">{{ $contribution->paid_at->format('d/m/Y') }}</td>
                            <td style="font-weight:var(--weight-semibold)">{{ number_format($contribution->amount, 0, ',', ' ') }}</td>
                            <td class="muted">{{ $contribution->created_at->format('d/m/Y') }}</td>
                            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                                <td class="actions">
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('contributions.edit', $contribution) }}" data-turbo-frame="modal" aria-label="Modifier">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <form class="inline confirm-delete" method="POST" action="{{ route('contributions.destroy', $contribution) }}">
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
                            <td colspan="{{ in_array(auth()->user()->role, ['admin', 'gestionnaire'], true) ? 7 : 6 }}">
                                <div class="empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="10"/><path d="M16 8l-4 4-4-4"/><path d="M12 16V8"/>
                                    </svg>
                                    <p>Aucune cotisation pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="pagination-wrap">{{ $contributions->links() }}</div>
</x-layouts.app>
