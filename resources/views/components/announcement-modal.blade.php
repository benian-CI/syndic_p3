@props([
    'pageTitle',
    'heading',
    'breadcrumb' => null,
])

@if (! request()->header('Turbo-Frame'))
    <x-layouts.app :title="$pageTitle">
@endif

<turbo-frame id="modal">
    <div class="modal-backdrop">
        <div class="modal-container">
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer">&times;</button>

            <div class="topbar">
                <div class="topbar-left">
                    <nav class="breadcrumb" aria-label="Fil d'Ariane">
                        <a href="{{ route('announcements.index') }}" data-turbo-frame="_top">Annonces</a>
                        <span class="breadcrumb-sep" aria-hidden="true">/</span>
                        <span class="breadcrumb-current">{{ $breadcrumb ?? $heading }}</span>
                    </nav>
                    <h1>{{ $heading }}</h1>
                </div>
                <button type="button" class="btn secondary" data-modal-close>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Retour
                </button>
            </div>

            <section class="panel">
                {{ $slot }}
            </section>
        </div>
    </div>
</turbo-frame>

@if (! request()->header('Turbo-Frame'))
    </x-layouts.app>
@endif
