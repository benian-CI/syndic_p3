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
               placeholder="Rechercher un lieu (ex : Cocody, Rive Gauche, Abidjan)">
        <div id="street-map-search-results" class="map-search-results"></div>
    </div>
    <div id="street-map-picker" class="map-picker"
         data-lat="{{ old('latitude', $street->latitude ?? '') }}"
         data-lng="{{ old('longitude', $street->longitude ?? '') }}"></div>
    <input type="hidden" name="latitude" id="street-latitude" value="{{ old('latitude', $street->latitude ?? '') }}">
    <input type="hidden" name="longitude" id="street-longitude" value="{{ old('longitude', $street->longitude ?? '') }}">
    <p class="map-picker-hint">Recherche un lieu ci-dessus ou clique directement sur la carte pour placer/ajuster le repère.</p>
</label>
