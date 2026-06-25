<label>Nom
    <input name="name" value="{{ old('name', $user->name ?? '') }}"
           class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
           placeholder="Prénom Nom" required>
    @error('name')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Email
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
           class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
           placeholder="utilisateur@exemple.com" required>
    @error('email')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Rôle
    <select name="role" class="{{ $errors->has('role') ? 'is-invalid' : '' }}" required>
        @foreach ($roles as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $user->role ?? 'lecteur') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('role')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Mot de passe
    <input type="password" name="password"
           class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
           placeholder="{{ isset($user) ? 'Laisser vide pour conserver l\'ancien' : '••••••••' }}"
           @required(!isset($user))>
    @error('password')<span class="field-error">{{ $message }}</span>@enderror
    @isset($user)<span class="helper-text">Laissez vide pour conserver le mot de passe actuel.</span>@endisset
</label>
<label>Confirmer le mot de passe
    <input type="password" name="password_confirmation"
           class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
           placeholder="••••••••"
           @required(!isset($user))>
    @error('password_confirmation')<span class="field-error">{{ $message }}</span>@enderror
</label>
