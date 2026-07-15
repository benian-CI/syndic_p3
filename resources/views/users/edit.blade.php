<x-modal title="Modifier un utilisateur" subtitle="Modifie les informations ou le rôle de cet utilisateur." back="{{ route('users.index') }}">
    <x-slot:icon>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </x-slot:icon>

    <form method="POST" action="{{ route('users.update', $user) }}" data-turbo-frame="_top">
        @csrf @method('PUT')
        <div class="modal-body form-grid">
            @include('users.form')
        </div>
        <div class="modal-foot">
            <a href="{{ route('users.index') }}" class="btn secondary">Annuler</a>
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
