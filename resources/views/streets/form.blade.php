<label>Nom de la rue
    <input name="name" value="{{ old('name', $street->name ?? '') }}"
           class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
           placeholder="Ex : Allée des Palmiers" required>
    @error('name')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label class="full">Description
    <textarea name="description"
              class="{{ $errors->has('description') ? 'is-invalid' : '' }}"
              placeholder="Description optionnelle de la rue...">{{ old('description', $street->description ?? '') }}</textarea>
    @error('description')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label class="full">Position sur la carte (optionnel)
    <div class="map-search">
        <input type="text" id="street-map-search" autocomplete="off"
               placeholder="Rechercher une rue du quartier Rosiers 3e Programme Rive Gauche">
        <div id="street-map-search-results" class="map-search-results"></div>
    </div>
    <div id="street-map-picker" class="map-picker" data-restrict="quartier"
         data-lat="{{ old('latitude', $street->latitude ?? '') }}"
         data-lng="{{ old('longitude', $street->longitude ?? '') }}"></div>
    <input type="hidden" name="latitude" id="street-latitude" value="{{ old('latitude', $street->latitude ?? '') }}">
    <input type="hidden" name="longitude" id="street-longitude" value="{{ old('longitude', $street->longitude ?? '') }}">
    <p class="map-picker-hint">Clique dans le champ de recherche pour choisir une rue du quartier {{ config('quartier.name') }}, ou clique directement sur la carte (zone en pointillés, rayon {{ \App\Support\Quartier::radiusKm() }} km).</p>
    <span class="field-error" id="street-map-warning" hidden>Ce point est hors du quartier {{ config('quartier.name') }}.</span>
    @error('latitude')<span class="field-error">{{ $message }}</span>@enderror
</label>
