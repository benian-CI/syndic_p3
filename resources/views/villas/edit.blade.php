<x-modal title="Modifier une villa" subtitle="Modifie les informations du bien ou de son propriétaire." back="{{ route('villas.index') }}">
    <x-slot:icon>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('villas.update', $villa) }}" data-turbo-frame="_top">
        @csrf @method('PUT')
        <div class="modal-body form-grid">
            @include('villas.form')
        </div>
        <div class="modal-foot">
            <a href="{{ route('villas.index') }}" class="btn secondary">Annuler</a>
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
