<x-layouts.app title="Ressources">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Ressources</h1>
            <p class="muted">Suivi des ressources et paiements associes.</p>
        </div>
        <div class="actions">
            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                <a class="btn" href="{{ route('events.create') }}" data-turbo-frame="modal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Ajouter une ressource
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('events.index') }}" class="filter-bar search-filter">
        <label>Recherche
            <input name="q" value="{{ request('q') }}" placeholder="Titre, reference, methode, description">
        </label>
        <label>Date debut
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
                        <th>Mois</th>
                        <th>Ressource</th>
                        <th>Montant</th>
                        <th>Date paiement</th>
                        <th>Methode</th>
                        <th>Reference</th>
                        <th>Cree le</th>
                        @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                            <th><span class="sr-only">Actions</span></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td class="muted" style="white-space:nowrap">{{ $event->month?->format('m/Y') ?? '-' }}</td>
                            <td>
                                <div style="font-weight:var(--weight-medium)">{{ $event->title }}</div>
                                @if ($event->description)
                                    <div class="muted">{{ $event->description }}</div>
                                @endif
                            </td>
                            <td class="muted">{{ number_format($event->amount, 0, ',', ' ') }}</td>
                            <td class="muted">{{ $event->paid_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="muted">{{ $event->payment_method ?: '-' }}</td>
                            <td class="muted">{{ $event->reference ?: '-' }}</td>
                            <td class="muted">{{ $event->created_at->format('d/m/Y') }}</td>
                            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                                <td class="actions">
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('events.edit', $event) }}" data-turbo-frame="modal" aria-label="Modifier">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <form class="inline confirm-delete" method="POST" action="{{ route('events.destroy', $event) }}">
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
                            <td colspan="{{ in_array(auth()->user()->role, ['admin', 'gestionnaire'], true) ? 8 : 7 }}">
                                <div class="empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <p>Aucune ressource pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination-wrap">{{ $events->links() }}</div>
</x-layouts.app>
