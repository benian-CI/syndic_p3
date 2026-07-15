<x-modal title="Modifier l'annonce" subtitle="Modifie le contenu de cette annonce." back="{{ route('announcements.index') }}">
    <x-slot:icon>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('announcements.update', $announcement) }}" data-turbo-frame="_top">
        @csrf @method('PUT')
        <div class="modal-body form-grid">
            @include('announcements.form', ['announcement' => $announcement])
        </div>
        <div class="modal-foot">
            <a href="{{ route('announcements.index') }}" class="btn secondary">Annuler</a>
            <button class="btn" type="submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Mettre à jour
            </button>
        </div>
    </form>
</x-modal>
