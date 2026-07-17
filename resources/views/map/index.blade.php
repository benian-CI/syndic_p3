<x-layouts.app title="Carte du quartier">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Carte du quartier</h1>
            <p class="muted">Localisation des rues du Programme 3 Rive Gauche.</p>
        </div>
    </div>

    <section class="panel">
        @if ($streets->isEmpty())
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <p>Aucune rue géolocalisée pour le moment. Ajoute un repère depuis le formulaire d'une rue.</p>
            </div>
        @else
            <div id="overview-map" class="overview-map"></div>
        @endif
    </section>

    @php
        $mapStreetsData = $streets->map(function ($street) {
            return [
                'name' => $street->name,
                'villasCount' => $street->villas_count,
                'latitude' => (float) $street->latitude,
                'longitude' => (float) $street->longitude,
            ];
        })->values();
    @endphp
    <script>
        window.__mapStreets = @json($mapStreetsData);
    </script>
</x-layouts.app>
