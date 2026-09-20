<x-modal title="Ajouter un syndic partenaire" subtitle="Référence un syndic client du réseau pour qu'il apparaisse sur la carte." back="{{ route('partner-syndics.index') }}">
    <x-slot:icon>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('partner-syndics.store') }}" data-turbo-frame="_top">
        @csrf
        <div class="modal-body form-grid">
            @include('partner-syndics.form')
        </div>
        <div class="modal-foot">
            <a href="{{ route('partner-syndics.index') }}" class="btn secondary">Annuler</a>
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
