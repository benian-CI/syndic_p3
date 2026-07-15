<x-layouts.app title="Rues">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Rues</h1>
            <p class="muted">Organisez les villas par rue.</p>
        </div>
        <div class="actions">
            <x-export-buttons resource="streets" />
            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                <a class="btn" href="{{ route('streets.create') }}" data-turbo-frame="modal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Ajouter une rue
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('streets.index') }}" class="filter-bar streets-filter">
        <label>Nom de la rue
            <input name="q" value="{{ request('q') }}" placeholder="Ex : Palmiers">
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
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Villas</th>
                        <th>Créée le</th>
                        @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                            <th><span class="sr-only">Actions</span></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($streets as $street)
                        <tr>
                            <td style="font-weight:var(--weight-medium)">{{ $street->name }}</td>
                            <td class="muted">{{ $street->description ?: '—' }}</td>
                            <td><span class="badge badge-blue">{{ $street->villas_count }}</span></td>
                            <td class="muted">{{ $street->created_at->format('d/m/Y') }}</td>
                            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                                <td class="actions">
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('streets.edit', $street) }}" data-turbo-frame="modal" aria-label="Modifier">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <form class="inline confirm-delete" method="POST" action="{{ route('streets.destroy', $street) }}">
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
                            <td colspan="{{ in_array(auth()->user()->role, ['admin', 'gestionnaire'], true) ? 5 : 4 }}">
                                <div class="empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M9 20l-5.447-2.724A1 1 0 0 1 3 16.382V5.618a1 1 0 0 1 .553-.894L9 2l5.447 2.724A1 1 0 0 1 15 5.618v10.764a1 1 0 0 1-.553.894L9 20z"/>
                                    </svg>
                                    <p>Aucune rue pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination-wrap">{{ $streets->links() }}</div>
</x-layouts.app>
