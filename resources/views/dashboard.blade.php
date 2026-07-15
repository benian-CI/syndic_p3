<x-layouts.app title="Dashboard">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Tableau de bord</h1>
            <p class="muted">Vue globale des dépenses, cotisations et villas du quartier.</p>
        </div>
        {{-- <a class="btn" href="{{ route('contributions.create') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Ajouter une cotisation
        </a> --}}
    </div>

    {{-- Ligne 1 : KPIs principaux --}}
    <div class="grid stats">
        <div class="card">
            <div class="card-icon icon-expense">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="6" width="20" height="12" rx="2" ry="2" />
                    <circle cx="12" cy="12" r="2" />
                    <line x1="6" y1="12" x2="6.01" y2="12" />
                    <line x1="18" y1="12" x2="18.01" y2="12" />
                </svg>
            </div>
            <div class="stat-label">Total des dépenses</div>
            <div class="stat-value">{{ number_format($totalExpenses, 0, ',', ' ') }}</div>
        </div>
        <div class="card">
            <div class="card-icon icon-income">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                    <polyline points="17 6 23 6 23 12" />
                </svg>
            </div>
            <div class="stat-label">Total des cotisations</div>
            <div class="stat-value">{{ number_format($totalContributions, 0, ',', ' ') }}</div>
        </div>
        <div class="card">
            <div class="card-icon icon-villas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6" />
                </svg>
            </div>
            <div class="stat-label">Villas</div>
            <div class="stat-value">{{ $contributingVillas }} <span
                    style="font-size:var(--text-base);font-weight:var(--weight-normal);color:var(--muted)">/
                    {{ $totalVillas }}</span></div>
            <div class="stat-footer">villas cotisantes ce mois</div>
        </div>
        <div class="card">
            <div class="card-icon icon-owners">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div class="stat-label">Propriétaires</div>
            <div class="stat-value">{{ $totalOwners }}</div>
        </div>
    </div>

    {{-- Ligne 2 : Période --}}
    <div class="grid stats" style="margin-top: var(--space-4);">
        <div class="card">
            <div class="card-icon icon-month">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
            </div>
            <div class="stat-label">Cotisations Mensuelles</div>
            <div class="stat-value">{{ number_format($monthlyContributions, 0, ',', ' ') }}</div>
            <div class="stat-footer">{{ $monthlyContributingVillas }} villa(s)</div>
        </div>
        <div class="card">
            <div class="card-icon icon-year">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
            </div>
            <div class="stat-label">Cotisations Annuelles en cours</div>
            <div class="stat-value">{{ number_format($contributionsThisYear, 0, ',', ' ') }}</div>
        </div>
        <div class="card">
            <div class="card-icon icon-month">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="6" width="20" height="12" rx="2" ry="2" />
                    <circle cx="12" cy="12" r="2" />
                    <line x1="6" y1="12" x2="6.01" y2="12" />
                    <line x1="18" y1="12" x2="18.01" y2="12" />
                </svg>
            </div>
            <div class="stat-label">Dépenses Mensuelles</div>
            <div class="stat-value">{{ number_format($expensesThisMonth, 0, ',', ' ') }}</div>
        </div>
        <div class="card">
            <div class="card-icon icon-year">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="6" width="20" height="12" rx="2" ry="2" />
                    <circle cx="12" cy="12" r="2" />
                    <line x1="6" y1="12" x2="6.01" y2="12" />
                    <line x1="18" y1="12" x2="18.01" y2="12" />
                </svg>
            </div>
            <div class="stat-label">Dépenses Annuelles en cours</div>
            <div class="stat-value">{{ number_format($expensesThisYear, 0, ',', ' ') }}</div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="grid two">
        <section class="panel">
            <h2>Répartition ce mois</h2>
            <div class="chart-wrap">
                <canvas id="monthlyChart"></canvas>
            </div>
        </section>
        <section class="panel">
            <h2>Cotisations vs Dépenses — Année en cours</h2>
            <div class="chart-wrap">
                <canvas id="annualChart"></canvas>
            </div>
        </section>
    </div>

    {{-- Tables récentes --}}
    <div class="grid two">
        <section class="panel">
            <h2>Dernières cotisations</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Villa</th>
                            <th>Mois</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentContributions as $payment)
                            <tr>
                                <td>
                                    <div style="font-weight:var(--weight-medium)">{{ $payment['primary'] }}</div>
                                    <div class="muted">{{ $payment['secondary'] }}</div>
                                </td>
                                <td>{{ $payment['month']?->format('m/Y') ?? '—' }}</td>
                                <td style="font-weight:var(--weight-semibold)">
                                    {{ number_format($payment['amount'], 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="3">
                                    <div class="empty-state">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M16 8l-4 4-4-4" />
                                            <path d="M12 16V8" />
                                        </svg>
                                        <p>Aucune cotisation enregistrée.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section class="panel">
            <h2>Dernières dépenses</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Dépense</th>
                            <th>Date</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentExpenses as $expense)
                            <tr>
                                <td>
                                    <div style="font-weight:var(--weight-medium)">{{ $expense->title }}</div>
                                    @if($expense->category)
                                        <div class="muted">{{ $expense->category }}</div>
                                    @endif
                                </td>
                                <td>{{ $expense->spent_at->format('d/m/Y') }}</td>
                                <td style="font-weight:var(--weight-semibold)">
                                    {{ number_format($expense->amount, 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="3">
                                    <div class="empty-state">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5">
                                            <rect x="2" y="6" width="20" height="12" rx="2" ry="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <line x1="6" y1="12" x2="6.01" y2="12" />
                                            <line x1="18" y1="12" x2="18.01" y2="12" />
                                        </svg>
                                        <p>Aucune dépense enregistrée.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            window.__dashboardData = {
                contributionsThisMonth: {{ $contributionsThisMonth }},
                expensesThisMonth: {{ $expensesThisMonth }},
                contributionsThisYear: {{ $contributionsThisYear }},
                expensesThisYear: {{ $expensesThisYear }}
            };
        </script>
    @endpush
</x-layouts.app>
