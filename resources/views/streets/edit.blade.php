<x-modal title="Modifier une rue" subtitle="Modifie le nom ou la description de la rue." back="{{ route('streets.index') }}">
    <x-slot:icon>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 20l-5.447-2.724A1 1 0 0 1 3 16.382V5.618a1 1 0 0 1 .553-.894L9 2l5.447 2.724A1 1 0 0 1 15 5.618v10.764a1 1 0 0 1-.553.894L9 20z"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('streets.update', $street) }}" data-turbo-frame="_top">
        @csrf @method('PUT')
        <div class="modal-body form-grid">
            @include('streets.form')
        </div>
        <div class="modal-foot">
            <a href="{{ route('streets.index') }}" class="btn secondary">Annuler</a>
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
