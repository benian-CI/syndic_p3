<x-layouts.app title="Détail de l'annonce">
    <div class="topbar">
        <div class="topbar-left">
            <nav class="breadcrumb" aria-label="Fil d'Ariane">
                <a href="{{ route('announcements.index') }}">Annonces</a>
                <span class="breadcrumb-sep" aria-hidden="true">/</span>
                <span class="breadcrumb-current">Détail</span>
            </nav>
            <h1>Détail de l'annonce</h1>
        </div>
        <div class="actions">
            <a class="btn secondary" href="{{ route('announcements.index') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Retour
            </a>
            <a class="btn secondary export-pdf" href="{{ route('announcements.pdf', $announcement) }}" data-turbo="false">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Télécharger le PDF
            </a>
            @if (! $announcement->sent_at && in_array(auth()->user()->role, ['admin', 'gestionnaire'], true))
                <a class="btn secondary" href="{{ route('announcements.edit', $announcement) }}" data-turbo-frame="modal">Modifier</a>
                <a class="btn" href="{{ route('announcements.send', $announcement) }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Envoyer
                </a>
            @endif
        </div>
    </div>

    <section class="panel">
        <div class="grid two" style="margin-bottom: var(--space-5);">
            <div>
                <div class="stat-label">Destinataire</div>
                <div style="font-weight:var(--weight-medium)">{{ $announcement->destinataire }}</div>
            </div>
            <div>
                <div class="stat-label">Canal</div>
                <span class="badge badge-blue">{{ ucfirst($announcement->channel) }}</span>
            </div>
            <div>
                <div class="stat-label">Statut</div>
                @if ($announcement->sent_at)
                    <span class="badge badge-success">Envoyée le {{ $announcement->sent_at->format('d/m/Y H:i') }}</span>
                @else
                    <span class="badge badge-neutral">En attente</span>
                @endif
            </div>
            <div>
                <div class="stat-label">Créée le</div>
                <div class="muted">{{ $announcement->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <h2 style="margin-bottom: var(--space-2);">{{ $announcement->title }}</h2>
        <p style="white-space:pre-wrap;line-height:1.6">{{ $announcement->message }}</p>
    </section>
</x-layouts.app>
