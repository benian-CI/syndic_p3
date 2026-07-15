<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Gestion Quartier</title>
    @vite(['resources/css/app.css'])
</head>
<body>
     {{-- {{ Hash::make('password') }} --}}
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <img src="{{ asset('assets/logo-mark.png') }}" alt="Syndic P3 Rive Gauche" class="login-brand-logo">
                <div class="login-brand-name">SYNDIC P3 RIVE GAUCHE</div>
                <div class="login-brand-sub">Syndic Management</div>
            </div>

            <div class="login-title">Connexion</div>
            <div class="login-subtitle">Accédez à la gestion du quartier.</div>

            @if ($errors->any())
                <div class="errors" role="alert" style="margin-bottom: var(--space-4);">
                    <div style="display:flex;align-items:center;gap:8px">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('login.store') }}">
                @csrf
                <label>
                    Adresse e-mail
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="vous@exemple.com"
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                           required autofocus>
                </label>
                <label>
                    Mot de passe
                    <input type="password" name="password"
                           placeholder="••••••••"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                           required>
                </label>
                <label class="checkbox-label" style="font-weight: var(--weight-normal);">
                    <input type="checkbox" name="remember" value="1">
                    Se souvenir de moi
                </label>
                <button type="submit" class="btn" style="width:100%; justify-content:center; padding: 11px 16px;">
                    Se connecter
                </button>
            </form>

            <div class="login-footer">
                &copy; {{ date('Y') }} Gestion Quartier
            </div>
        </div>
    </div>
</body>
</html>
