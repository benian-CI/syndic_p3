<x-layouts.app title="Villas">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Villas et propriétaires</h1>
            <p class="muted">Liste des villas, rues, propriétaires et contacts.</p>
        </div>
        <div class="actions">
            <x-export-buttons resource="villas" />
            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                <a class="btn" href="{{ route('villas.create') }}" data-turbo-frame="modal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Ajouter une villa
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('villas.index') }}" class="filter-bar search-filter">
        <label>Recherche
            <input name="q" value="{{ request('q') }}" placeholder="Villa, propriétaire, téléphone, email, rue">
        </label>
        <label>Créée depuis
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
            <a class="btn secondary" href="{{ route('villas.index') }}">Effacer</a>
        </div>
    </form>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Villa</th>
                        <th>Rue</th>
                        <th>Propriétaire</th>
                        <th>Contact</th>
                        <th>Cotisations</th>
                        <th>Créée le</th>
                        @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                            <th><span class="sr-only">Actions</span></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($villas as $villa)
                        <tr>
                            <td style="font-weight:var(--weight-semibold)">{{ $villa->number }}</td>
                            <td>{{ optional($villa->street)->name ?: '—' }}</td>
                            <td style="font-weight:var(--weight-medium)">{{ $villa->owner_name }}</td>
                            <td>
                                @if($villa->owner_email)<div class="text-sm">{{ $villa->owner_email }}</div>@endif
                                @if($villa->owner_phone)<div class="muted">{{ $villa->owner_phone }}</div>@endif
                                @if(!$villa->owner_email && !$villa->owner_phone)<span class="muted">—</span>@endif
                            </td>
                            <td style="font-weight:var(--weight-medium)">{{ number_format($villa->contributions_sum_amount ?? 0, 0, ',', ' ') }}</td>
                            <td class="muted">{{ $villa->created_at->format('d/m/Y') }}</td>
                            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                                <td class="actions">
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('villas.edit', $villa) }}" data-turbo-frame="modal" aria-label="Modifier">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <form class="inline confirm-delete" method="POST" action="{{ route('villas.destroy', $villa) }}">
                                            @csrf @method('DELETE')
                                            <button class="icon-btn danger" type="submit" aria-label="Supprimer">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6M14 11v6"/>
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
                                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>
                                    </svg>
                                    <p>Aucune villa pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="pagination-wrap">{{ $villas->links() }}</div>
</x-layouts.app>
