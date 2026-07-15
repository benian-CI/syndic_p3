<x-modal title="Ajouter une dépense" subtitle="Enregistre une dépense engagée pour le quartier." back="{{ route('expenses.index') }}">
    <x-slot:icon>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2" ry="2"/><circle cx="12" cy="12" r="2"/><line x1="6" y1="12" x2="6.01" y2="12"/><line x1="18" y1="12" x2="18.01" y2="12"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('expenses.store') }}" data-turbo-frame="_top">
        @csrf
        <div class="modal-body form-grid">
            @include('expenses.form')
        </div>
        <div class="modal-foot">
            <a href="{{ route('expenses.index') }}" class="btn secondary">Annuler</a>
            <button class="btn" type="submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>
</x-modal>
