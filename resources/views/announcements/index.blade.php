<x-layouts.app title="Annonces">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Annonces</h1>
            <p class="muted">Préparez une annonce, puis contactez les propriétaires par email, appel ou WhatsApp.</p>
        </div>
        <div class="actions">
            <x-export-buttons resource="announcements" />
            @if (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                <a class="btn" href="{{ route('announcements.create') }}" data-modal-url data-turbo="false">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nouvelle annonce
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('announcements.index') }}" class="filter-bar search-filter">
        <label>Recherche
            <input name="q" value="{{ request('q') }}" placeholder="Titre, destinataire, message, canal">
        </label>
        <label>Date d'envoi, du
            <input type="date" name="date_debut" value="{{ request('date_debut') }}">
        </label>
        <label>au
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

    <div>
        <section class="panel">
            <h2>Dernières annonces</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Destinataire</th>
                            <th>Titre</th>
                            <th>Canal</th>
                            <th>Date envoi</th>
                            <th>Créée le</th>
                            <th><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($announcements as $announcement)
                            <tr>
                                <td class="muted">{{ $announcement->destinataire }}</td>
                                <td>
                                    <div style="font-weight:var(--weight-medium)">{{ $announcement->title }}</div>
                                    <div class="muted" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $announcement->message }}</div>
                                </td>
                                <td>
                                    @php
                                        $channelBadge = match($announcement->channel) {
                                            'whatsapp' => 'badge-success',
                                            'phone' => 'badge-amber',
                                            default => 'badge-blue',
                                        };
                                        $channelLabel = match($announcement->channel) {
                                            'phone' => 'Téléphone',
                                            'whatsapp' => 'WhatsApp',
                                            default => 'Email',
                                        };
                                    @endphp
                                    <span class="badge {{ $channelBadge }}">{{ $channelLabel }}</span>
                                </td>
                                <td class="muted">{{ $announcement->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="muted">{{ $announcement->created_at->format('d/m/Y') }}</td>
                                <td class="actions">
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('announcements.show', $announcement) }}" aria-label="Détail">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        @if ($announcement->sent_at)
                                            <span class="badge badge-success">Envoyée</span>
                                        @elseif (in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                                            <a class="icon-btn" href="{{ route('announcements.edit', $announcement) }}" data-turbo-frame="modal" aria-label="Modifier">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </a>
                                            <a class="icon-btn" href="{{ route('announcements.send', $announcement) }}" aria-label="Envoyer">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                                </svg>
                                            </a>
                                            <form class="inline confirm-delete" method="POST" action="{{ route('announcements.destroy', $announcement) }}" data-turbo-frame="_top">
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="6">
                                    <div class="empty-state">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                        </svg>
                                        <p>Aucune annonce créée.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $announcements->links() }}</div>
        </section>

    </div>
</x-layouts.app>
