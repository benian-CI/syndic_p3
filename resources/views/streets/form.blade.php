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
