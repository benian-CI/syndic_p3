<x-layouts.app title="Carte du quartier">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Carte du quartier</h1>
            <p class="muted">Localisation des villas du Programme 3 Rive Gauche.</p>
        </div>
    </div>

    <section class="panel">
        @if ($villas->isEmpty())
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <p>Aucune villa géolocalisée pour le moment. Ajoute un repère depuis le formulaire d'une villa.</p>
            </div>
        @else
            <div id="overview-map" class="overview-map"></div>
        @endif
    </section>

    @php
        $mapVillasData = $villas->map(function ($villa) {
            return [
                'number' => $villa->number,
                'ownerName' => $villa->owner_name,
                'streetName' => $villa->street?->name,
                'latitude' => (float) $villa->latitude,
                'longitude' => (float) $villa->longitude,
            ];
        })->values();
    @endphp
    <script>
        window.__mapVillas = @json($mapVillasData);
    </script>
</x-layouts.app>
