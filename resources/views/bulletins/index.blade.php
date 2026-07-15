<x-layouts.app title="Bulletins de Solde">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Bulletins de Solde</h1>
            <p class="muted">Sélectionnez une villa et une période pour générer le bulletin de solde.</p>
        </div>
    </div>

    <div class="panel" style="margin-bottom: var(--space-5);">
        <form method="GET" action="{{ route('bulletins.index') }}" class="form-grid">
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
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    Voir le bulletin
                </button>
            </div>
        </form>
    </div>

    @if($selectedVilla)
        <div class="panel">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:var(--space-4);margin-bottom:var(--space-4);flex-wrap:wrap">
                <div>
                    <h2 style="margin-bottom:var(--space-1)">Bulletin de solde — Villa {{ $selectedVilla->number }}</h2>
                    <p class="muted">{{ $selectedVilla->owner_name }} &bull; Période : {{ $start->format('d/m/Y') }} → {{ $end->format('d/m/Y') }}</p>
                </div>
                <div class="row-actions">
                    <a class="btn secondary export-pdf" href="{{ route('bulletins.index', ['villa_id' => $selectedVilla->id, 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d'), 'format' => 'pdf']) }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Télécharger PDF
                    </a>
                    <a class="btn secondary"
                       href="{{ route('bulletins.index', ['villa_id' => $selectedVilla->id, 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d'), 'format' => 'stream']) }}"
                       target="_blank">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        Imprimer
                    </a>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date de paiement</th>
                            <th>Mois</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Référence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contributions as $contribution)
                            <tr>
                                <td>{{ $contribution->paid_at->format('d/m/Y') }}</td>
                                <td><span class="badge badge-neutral">{{ $contribution->month->format('m/Y') }}</span></td>
                                <td style="font-weight:var(--weight-semibold)">{{ number_format($contribution->amount, 2, ',', ' ') }} FCFA</td>
                                <td class="muted">{{ $contribution->payment_method ?? '—' }}</td>
                                <td class="muted">{{ $contribution->reference ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="5">
                                    <div class="empty-state">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                        <p>Aucune cotisation trouvée pour cette période.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <span style="font-size:var(--text-lg);font-weight:var(--weight-bold)">
                    Total : <span style="color:var(--primary)">{{ number_format($total, 2, ',', ' ') }} FCFA</span>
                </span>
            </div>
        </div>
    @endif
</x-layouts.app>
