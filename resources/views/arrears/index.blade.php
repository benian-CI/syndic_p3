<x-layouts.app title="Arriérés de paiement">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Arriérés de paiement</h1>
            <p class="muted">Sélectionnez une villa et une période pour voir les mois non payés.</p>
        </div>
    </div>

    <div class="panel" style="margin-bottom: var(--space-5);">
        <form method="GET" action="{{ route('arrears.index') }}" class="form-grid">
            <label>Villa
                <select name="villa_id">
                    <option value="">— Choisir une villa —</option>
                    @foreach($villas as $villa)
                        <option value="{{ $villa->id }}" {{ optional($selectedVilla)->id == $villa->id ? 'selected' : '' }}>
                            {{ $villa->number }} — {{ optional($villa->street)->name ?: '—' }} ({{ $villa->owner_name }})
                        </option>
                    @endforeach
                </select>
            </label>
            <label>Début
                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}">
            </label>
            <label>Fin
                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}">
            </label>
            <div class="filter-actions">
                <button class="btn" type="submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Voir l'arriéré
                </button>
                @if($selectedVilla)
                    <a class="btn secondary" href="{{ route('arrears.index', ['villa_id' => $selectedVilla->id, 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d'), 'format' => 'pdf']) }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Télécharger PDF
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($selectedVilla)
        <div class="panel">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:var(--space-4);margin-bottom:var(--space-4);flex-wrap:wrap">
                <div>
                    <h2 style="margin-bottom:var(--space-1)">Arriérés — Villa {{ $selectedVilla->number }}</h2>
                    <p class="muted">{{ $selectedVilla->owner_name }} &bull; Période : {{ $start->format('d/m/Y') }} → {{ $end->format('d/m/Y') }}</p>
                </div>
                <a class="btn secondary"
                   href="{{ route('arrears.index', ['villa_id' => $selectedVilla->id, 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d'), 'format' => 'stream']) }}"
                   target="_blank">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Imprimer
                </a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mois</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($arrears as $arrear)
                            <tr>
                                <td style="font-weight:var(--weight-medium)">{{ $arrear['month']->format('F Y') }}</td>
                                <td><span class="badge badge-danger">Non payé</span></td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="2">
                                    <div class="empty-state">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                        <p>Aucun arriéré trouvé — tout est à jour !</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <span style="font-size:var(--text-lg);font-weight:var(--weight-bold)">
                    Mois impayés :
                    <span style="color:{{ $arrears->count() > 0 ? 'var(--danger)' : 'var(--success-text)' }}">
                        {{ $arrears->count() }}
                    </span>
                </span>
            </div>
        </div>
    @endif
</x-layouts.app>
