<x-layouts.app title="Nouvelle annonce">
    <turbo-frame id="modal">
        <div class="modal-backdrop">
            <div class="modal-container">
                <button type="button" class="modal-close" data-modal-close>&times;</button>

                <div class="topbar">
                    <div class="topbar-left">
                        <nav class="breadcrumb" aria-label="Fil d'Ariane">
                            <a href="{{ route('announcements.index') }}">Annonces</a>
                            <span class="breadcrumb-sep" aria-hidden="true">/</span>
                            <span class="breadcrumb-current">Nouvelle annonce</span>
                        </nav>
                        <h1>Nouvelle annonce</h1>
                    </div>
                    <a class="btn secondary" href="{{ route('announcements.index') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                        </svg>
                        Retour
                    </a>
                </div>

                <section class="panel">
                    <form method="POST" action="{{ route('announcements.store') }}" class="form-grid" data-turbo-frame="_top">
                        @csrf
                        @include('announcements.form')
                        <div class="full">
                            <button class="btn" type="submit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                Preparer l'annonce
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </turbo-frame>
</x-layouts.app>
