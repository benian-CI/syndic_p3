<x-layouts.app title="Envoyer l'annonce">
    <div class="topbar">
        <div>
            <h1>Envoyer l'annonce</h1>
            <p class="muted">Utilisez les liens ci-dessous pour contacter les propriétaires selon le canal sélectionné.</p>
        </div>
        <div class="actions">
            <a class="btn secondary" href="{{ route('announcements.index') }}">Retour</a>
            <a class="btn secondary" href="{{ route('announcements.pdf', $announcement) }}" data-turbo="false">Télécharger le PDF</a>
            @if (! $announcement->sent_at)
                <form method="POST" action="{{ route('announcements.dispatch', $announcement) }}" class="inline" data-turbo-frame="_top">
                    @csrf
                    <button class="btn">
                        {{ $announcement->channel === 'email' && count($emailRecipients) > 0 ? 'Envoyer à tous par email' : 'Marquer comme envoyé' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <section class="panel">
        <h2>{{ $announcement->title }}</h2>
        <p class="muted">Canal : {{ ucfirst($announcement->channel) }} | Statut : {{ $announcement->sent_at ? 'Envoyée' : 'En attente' }}</p>
        <p>{{ $announcement->message }}</p>
    </section>

    <section class="panel" style="margin-top: 16px;">
        <h2>Contacts des propriétaires</h2>
        <table>
            <thead><tr><th>Propriétaire</th><th>Villa</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($villas as $villa)
                    @php
                        $phone = preg_replace('/\D+/', '', $villa->owner_phone ?? '');
                        $subject = urlencode($announcement->title);
                        $body = urlencode($announcement->message);
                    @endphp
                    <tr>
                        <td>{{ $villa->owner_name }}<br><span class="muted">{{ $villa->owner_email ?: $villa->owner_phone }}</span></td>
                        <td>Villa {{ $villa->number }}<br><span class="muted">{{ optional($villa->street)->name ?: '-' }}</span></td>
                        <td class="actions">
                            @if ($announcement->channel === 'email' && $villa->owner_email)
                                <a class="btn secondary" href="mailto:{{ $villa->owner_email }}?subject={{ $subject }}&body={{ $body }}">Email</a>
                            @endif
                            @if ($announcement->channel === 'phone' && $villa->owner_phone)
                                <a class="btn secondary" href="tel:{{ $villa->owner_phone }}">Appel</a>
                            @endif
                            @if ($announcement->channel === 'whatsapp' && $phone)
                                <a class="btn secondary" href="https://wa.me/{{ $phone }}?text={{ $body }}" target="_blank">WhatsApp</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="muted">Ajoutez des villas pour afficher les contacts.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrap">{{ $villas->links() }}</div>
    </section>
</x-layouts.app>
