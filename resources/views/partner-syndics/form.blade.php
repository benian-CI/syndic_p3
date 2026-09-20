<label>Nom du syndic
    <input name="name" value="{{ old('name', $partnerSyndic->name ?? '') }}"
           class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
           placeholder="Ex : Syndic Les Alizés" required>
    @error('name')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Ville
    <input name="city" value="{{ old('city', $partnerSyndic->city ?? '') }}"
           class="{{ $errors->has('city') ? 'is-invalid' : '' }}"
           placeholder="Ex : Abidjan">
    @error('city')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label class="full">Adresse
    <textarea name="address"
              class="{{ $errors->has('address') ? 'is-invalid' : '' }}"
              placeholder="Adresse complète (optionnel)">{{ old('address', $partnerSyndic->address ?? '') }}</textarea>
    @error('address')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Lien de connexion (URL)
    <input type="url" name="login_url" value="{{ old('login_url', $partnerSyndic->login_url ?? '') }}"
           class="{{ $errors->has('login_url') ? 'is-invalid' : '' }}"
           placeholder="https://leur-syndic.exemple.com">
    @error('login_url')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Statut
    <select name="status" class="{{ $errors->has('status') ? 'is-invalid' : '' }}">
        <option value="actif" @selected(old('status', $partnerSyndic->status ?? 'actif') === 'actif')>Actif</option>
        <option value="inactif" @selected(old('status', $partnerSyndic->status ?? 'actif') === 'inactif')>Inactif</option>
    </select>
    @error('status')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Contact (nom)
    <input name="contact_name" value="{{ old('contact_name', $partnerSyndic->contact_name ?? '') }}"
           class="{{ $errors->has('contact_name') ? 'is-invalid' : '' }}"
           placeholder="Nom du contact">
    @error('contact_name')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Contact (téléphone)
    <input name="contact_phone" value="{{ old('contact_phone', $partnerSyndic->contact_phone ?? '') }}"
           class="{{ $errors->has('contact_phone') ? 'is-invalid' : '' }}"
           placeholder="+225...">
    @error('contact_phone')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label class="full">Contact (email)
    <input type="email" name="contact_email" value="{{ old('contact_email', $partnerSyndic->contact_email ?? '') }}"
           class="{{ $errors->has('contact_email') ? 'is-invalid' : '' }}"
           placeholder="contact@exemple.com">
    @error('contact_email')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label class="full">Position sur la carte (optionnel)
    <div class="map-search">
        <input type="text" id="partner-map-search" autocomplete="off"
               placeholder="Rechercher un lieu (ex : Cocody, Abidjan)">
        <div id="partner-map-search-results" class="map-search-results"></div>
    </div>
    <div id="partner-map-picker" class="map-picker"
         data-lat="{{ old('latitude', $partnerSyndic->latitude ?? '') }}"
         data-lng="{{ old('longitude', $partnerSyndic->longitude ?? '') }}"></div>
    <input type="hidden" name="latitude" id="partner-latitude" value="{{ old('latitude', $partnerSyndic->latitude ?? '') }}">
    <input type="hidden" name="longitude" id="partner-longitude" value="{{ old('longitude', $partnerSyndic->longitude ?? '') }}">
    <p class="map-picker-hint">Recherche un lieu ci-dessus ou clique directement sur la carte pour placer/ajuster le repère de ce syndic.</p>
</label>
