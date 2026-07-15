<x-layouts.app title="Utilisateurs">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Utilisateurs</h1>
            <p class="muted">Gestion des comptes et des rôles.</p>
        </div>
        <a class="btn" href="{{ route('users.create') }}" data-turbo-frame="modal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter un utilisateur
        </a>
    </div>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Créé le</th>
                        <th><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:var(--space-3)">
                                    <div style="width:32px;height:32px;border-radius:var(--radius-full);background:var(--icon-blue-bg);color:var(--color-blue);display:flex;align-items:center;justify-content:center;font-size:var(--text-xs);font-weight:var(--weight-bold);text-transform:uppercase;flex-shrink:0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span style="font-weight:var(--weight-medium)">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="muted">{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleLabel = \App\Models\User::ROLES[$user->role] ?? $user->role;
                                    $badgeClass = match($user->role) {
                                        'admin' => 'badge-danger',
                                        'gestionnaire' => 'badge-blue',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $roleLabel }}</span>
                            </td>
                            <td class="muted">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="actions">
                                <div class="row-actions">
                                    <a class="icon-btn" href="{{ route('users.edit', $user) }}" data-turbo-frame="modal" aria-label="Modifier">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>
                                    <form class="inline confirm-delete" method="POST" action="{{ route('users.destroy', $user) }}">
                                        @csrf @method('DELETE')
                                        <button class="icon-btn danger" type="submit" aria-label="Supprimer">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="5">
                                <div class="empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <p>Aucun utilisateur.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination-wrap">{{ $users->links() }}</div>
</x-layouts.app>
