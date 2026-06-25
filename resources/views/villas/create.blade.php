<x-layouts.app title="Ajouter une villa">
    <turbo-frame id="modal">
        <div class="modal-backdrop">
            <div class="modal-container">
                <button type="button" class="modal-close" onclick="document.getElementById('modal').innerHTML=''">&times;</button>
    <div class="topbar">
        <div class="topbar-left">
            <nav class="breadcrumb" aria-label="Fil d'Ariane">
                <a href="{{ route('villas.index') }}">Villas</a>
                <span class="breadcrumb-sep" aria-hidden="true">/</span>
                <span class="breadcrumb-current">Ajouter</span>
            </nav>
            <h1>Ajouter une villa</h1>
        </div>
        <a class="btn secondary" href="{{ route('villas.index') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Retour
        </a>
    </div>
    <section class="panel">
        <form method="POST" action="{{ route('villas.store') }}" class="form-grid" data-turbo-frame="_top">
            @csrf
            @include('villas.form')
            <div class="full">
                <button class="btn" type="submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </section>
            </div>
        </div>
    </turbo-frame>
</x-layouts.app>
