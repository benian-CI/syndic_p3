<x-modal title="Modifier un événement" subtitle="Modifie les informations de cet événement." back="{{ route('events.index') }}">
    <x-slot:icon>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('events.update', $event) }}" data-turbo-frame="_top">
        @csrf @method('PUT')
        <div class="modal-body form-grid">
            @include('events.form', ['event' => $event])
        </div>
        <div class="modal-foot">
            <a href="{{ route('events.index') }}" class="btn secondary">Annuler</a>
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
