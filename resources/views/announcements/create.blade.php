<x-modal title="Nouvelle annonce" subtitle="Rédige une annonce à diffuser aux propriétaires." back="{{ route('announcements.index') }}">
    <x-slot:icon>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('announcements.store') }}" data-turbo-frame="_top">
        @csrf
        <div class="modal-body form-grid">
            @include('announcements.form')
        </div>
        <div class="modal-foot">
            <a href="{{ route('announcements.index') }}" class="btn secondary">Annuler</a>
            <button class="btn" type="submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                Préparer l'annonce
            </button>
        </div>
    </form>
</x-modal>
