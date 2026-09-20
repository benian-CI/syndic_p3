<x-layouts.app title="Carte du quartier">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Carte du quartier</h1>
            <p class="muted">Rues du quartier {{ config('quartier.name') }} et syndics partenaires du réseau.</p>
        </div>
        <div class="actions">
            @if ($showFinance)
                <form method="GET" action="{{ route('map.index') }}" class="map-month-form">
                    <label for="map-month">Mois</label>
                    <input type="month" id="map-month" name="month" value="{{ $month->format('Y-m') }}" onchange="this.form.submit()">
                </form>
            @endif
            @if (auth()->user()?->isAdmin())
                <form method="POST" action="{{ route('map.radius', request()->only('month')) }}" class="map-month-form">
                    @csrf
                    @method('PATCH')
                    <label for="map-radius">Rayon (km)</label>
                    <input type="number" id="map-radius" name="radius_km" value="{{ \App\Support\Quartier::radiusKm() }}" min="0.1" max="5" step="0.05" required style="width:90px">
                    <button type="submit" class="btn secondary">Appliquer</button>
                </form>
                <a class="btn secondary" href="{{ route('partner-syndics.index') }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                    </svg>
                    Gérer les syndics partenaires
                </a>
            @endif
        </div>
    </div>

    <section class="panel">
        @if ($streets->isEmpty() && $partnerSyndics->isEmpty())
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <p>Aucune rue géolocalisée pour le moment. Ajoute un repère depuis le formulaire d'une rue.</p>
            </div>
        @else
            @if ($showFinance && $summary)
                <div class="map-summary">
                    <div><span class="map-summary-value">{{ $summary['rate'] }} %</span><span class="muted">recouvrement en {{ $month->translatedFormat('F Y') }}</span></div>
                    <div><span class="map-summary-value">{{ $summary['paidVillas'] }} / {{ $summary['totalVillas'] }}</span><span class="muted">villas à jour</span></div>
                    <div><span class="map-summary-value">{{ number_format($summary['collected'], 0, ',', ' ') }} FCFA</span><span class="muted">encaissés</span></div>
                </div>
            @endif
            <div class="map-legend">
                @if ($showFinance)
                    <span class="map-legend-item"><span class="map-legend-dot map-legend-dot-ok"></span> ≥ 80 % payé</span>
                    <span class="map-legend-item"><span class="map-legend-dot map-legend-dot-mid"></span> 50 – 79 %</span>
                    <span class="map-legend-item"><span class="map-legend-dot map-legend-dot-low"></span> &lt; 50 %</span>
                @else
                    <span class="map-legend-item"><span class="map-legend-dot map-legend-dot-street"></span> Rues du quartier</span>
                @endif
                <span class="map-legend-item"><span class="map-legend-dot map-legend-dot-partner"></span> Syndics partenaires</span>
            </div>
            <div id="overview-map" class="overview-map"></div>
        @endif
    </section>

    @php
        $mapStreetsData = $streets->map(function ($street) use ($showFinance) {
            return [
                'name' => $street->name,
                'villasCount' => $street->villas_count,
                'paidCount' => $showFinance ? $street->paid_villas_count : null,
                'collected' => $showFinance ? $street->collected : null,
                'latitude' => (float) $street->latitude,
                'longitude' => (float) $street->longitude,
            ];
        })->values();

        $mapPartnersData = $partnerSyndics->map(function ($partner) {
            return [
                'name' => $partner->name,
                'city' => $partner->city,
                'loginUrl' => $partner->login_url,
                'contactName' => $partner->contact_name,
                'latitude' => (float) $partner->latitude,
                'longitude' => (float) $partner->longitude,
            ];
        })->values();
    @endphp
    <script>
        window.__mapStreets = @json($mapStreetsData);
        window.__mapMonthLabel = @json($showFinance ? $month->translatedFormat('F Y') : null);
        window.__partnerSyndics = @json($mapPartnersData);
    </script>
</x-layouts.app>
