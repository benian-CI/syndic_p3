<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Syndic P3' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-mark.png') }}">
    @php
        $appCss = Vite::asset('resources/css/app.css');
        $appJs = Vite::asset('resources/js/app.js');
    @endphp
    <link rel="preload" href="{{ $appCss }}" as="style">
    <link rel="preload" href="{{ $appJs }}" as="script">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.addEventListener("turbo:visit", () => {
            document.documentElement.classList.add("turbo-loading");
        });
        document.addEventListener("turbo:load", () => {
            document.documentElement.classList.remove("turbo-loading");
        });
    </script>
    <style>
        .turbo-progress-bar {
            height: 3px;
            background-color: #1b4da3;
        }
        html.turbo-loading main {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
    </style>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body>
    <button class="menu-toggle" type="button" aria-label="Ouvrir le menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 7h16M4 12h16M4 17h16" />
        </svg>
    </button>
    <div class="backdrop" aria-hidden="true"></div>

    <div class="shell">
        <aside>
            <div class="brand-row">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('assets/logo-mark.png') }}" alt="Syndic P3 Rive Gauche">
                </div>
                <div>
                    <div class="brand">SYNDIC P3 RIVE GAUCHE</div>
                    <div class="brand-sub">Syndic Management</div>
                </div>
                <button type="button" class="close-menu" aria-label="Fermer le menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>

            <nav>
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    Tableau de bord
                </a>
                <a class="{{ request()->routeIs('streets.*') ? 'active' : '' }}" href="{{ route('streets.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 20l-5.447-2.724A1 1 0 0 1 3 16.382V5.618a1 1 0 0 1 .553-.894L9 2l5.447 2.724A1 1 0 0 1 15 5.618v10.764a1 1 0 0 1-.553.894L9 20z"/>
                    </svg>
                    Rues
                </a>
                <a class="{{ request()->routeIs('villas.*') ? 'active' : '' }}" href="{{ route('villas.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>
                    </svg>
                    Villas
                </a>
                <a class="{{ request()->routeIs('contributions.*') ? 'active' : '' }}" href="{{ route('contributions.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M16 8l-4 4-4-4"/><path d="M12 16V8"/>
                    </svg>
                    Cotisations
                </a>
                <a class="{{ request()->routeIs('bulletins.*') ? 'active' : '' }}" href="{{ route('bulletins.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    Bulletins de Solde
                </a>
                <a class="{{ request()->routeIs('arrears.*') ? 'active' : '' }}" href="{{ route('arrears.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Arriérés de paiement
                </a>
                <a class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="6" width="20" height="12" rx="2" ry="2"/>
                        <circle cx="12" cy="12" r="2"/>
                        <line x1="6" y1="12" x2="6.01" y2="12"/>
                        <line x1="18" y1="12" x2="18.01" y2="12"/>
                    </svg>
                    Dépenses
                </a>
                <a class="{{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Ressource Additionnelle
                </a>
                <a class="{{ request()->routeIs('announcements.*') ? 'active' : '' }}" href="{{ route('announcements.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    Annonces
                </a>
                @if (auth()->user()?->isAdmin())
                    <a class="{{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        Utilisateurs
                    </a>
                @endif
            </nav>

            <div class="user-box">
                <div class="user-info">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->user()->name ?? '' }}</div>
                        <div class="user-role">{{ \App\Models\User::ROLES[auth()->user()->role ?? 'lecteur'] ?? '' }}</div>
                    </div>
                </div>
                <button type="button" class="logout-button" onclick="toggleTheme()" style="margin-bottom: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <span id="theme-text">Mode Sombre</span>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <script>
            // Theme toggling logic
            function updateThemeText() {
                const text = document.getElementById('theme-text');
                if (text) {
                    text.innerText = document.documentElement.classList.contains('dark') ? 'Mode Clair' : 'Mode Sombre';
                }
            }

            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
                updateThemeText();
            }

            // Init on load
            document.addEventListener("turbo:load", updateThemeText);
            updateThemeText();
        </script>

        <main>
            @if (session('success'))
                <div class="alert" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="errors" role="alert">
                    <div style="display:flex;align-items:flex-start;gap:8px">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <div>
                            @if ($errors->count() === 1)
                                {{ $errors->first() }}
                            @else
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>

    <turbo-frame id="modal"></turbo-frame>
    @stack('scripts')
</body>
</html>
