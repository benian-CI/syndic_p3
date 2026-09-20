<x-layouts.app title="Syndics partenaires">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Syndics partenaires</h1>
            <p class="muted">Annuaire des syndics clients du logiciel, affichés sur la carte pour interconnecter les installations.</p>
        </div>
        <div class="actions">
            <a class="btn secondary" href="{{ route('map.index') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                Voir la carte
            </a>
            <a class="btn" href="{{ route('partner-syndics.create') }}" data-turbo-frame="modal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Ajouter un syndic partenaire
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('partner-syndics.index') }}" class="filter-bar streets-filter">
        <label>Nom du syndic
            <input name="q" value="{{ request('q') }}" placeholder="Ex : Les Alizés">
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
                        <th>Ville</th>
                        <th>Contact</th>
                        <th>Statut</th>
                        <th>Ajouté le</th>
                        <th><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($partnerSyndics as $partner)
                        <tr>
                            <td style="font-weight:var(--weight-medium)">
                                {{ $partner->name }}
                                @if ($partner->login_url)
                                    <div class="muted"><a href="{{ $partner->login_url }}" target="_blank" rel="noopener noreferrer">{{ $partner->login_url }}</a></div>
                                @endif
                            </td>
                            <td class="muted">{{ $partner->city ?: '—' }}</td>
                            <td class="muted">
                                {{ $partner->contact_name ?: '—' }}
                                @if ($partner->contact_email)
                                    <div>{{ $partner->contact_email }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $partner->status === 'actif' ? 'badge-green' : 'badge-neutral' }}">
                                    {{ $partner->status === 'actif' ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="muted">{{ $partner->created_at->format('d/m/Y') }}</td>
                            <td class="actions">
                                <div class="row-actions">
                                    <a class="icon-btn" href="{{ route('partner-syndics.edit', $partner) }}" data-turbo-frame="modal" aria-label="Modifier">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>
                                    <form class="inline confirm-delete" method="POST" action="{{ route('partner-syndics.destroy', $partner) }}">
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
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="6">
                                <div class="empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                                    </svg>
                                    <p>Aucun syndic partenaire pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination-wrap">{{ $partnerSyndics->links() }}</div>
</x-layouts.app>
