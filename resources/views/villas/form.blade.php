<label>Rue
    <select name="street_id" class="{{ $errors->has('street_id') ? 'is-invalid' : '' }}" required>
        <option value="">Choisir une rue</option>
        @foreach ($streets as $street)
            <option value="{{ $street->id }}" @selected(old('street_id', $villa->street_id ?? '') == $street->id)>{{ $street->name }}</option>
        @endforeach
    </select>
    @error('street_id')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Numéro de villa
    <input name="number" value="{{ old('number', $villa->number ?? '') }}"
           class="{{ $errors->has('number') ? 'is-invalid' : '' }}"
           placeholder="Ex : A-12" required>
    @error('number')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Nom du propriétaire
    <input name="owner_name" value="{{ old('owner_name', $villa->owner_name ?? '') }}"
           class="{{ $errors->has('owner_name') ? 'is-invalid' : '' }}"
           placeholder="Prénom Nom" required>
    @error('owner_name')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Email
    <input type="email" name="owner_email" value="{{ old('owner_email', $villa->owner_email ?? '') }}"
           class="{{ $errors->has('owner_email') ? 'is-invalid' : '' }}"
           placeholder="proprietaire@exemple.com">
    @error('owner_email')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Téléphone / WhatsApp
    <input name="owner_phone" value="{{ old('owner_phone', $villa->owner_phone ?? '') }}"
           class="{{ $errors->has('owner_phone') ? 'is-invalid' : '' }}"
           placeholder="+225 07 00 00 00 00">
    @error('owner_phone')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Notes
    <input name="notes" value="{{ old('notes', $villa->notes ?? '') }}"
           class="{{ $errors->has('notes') ? 'is-invalid' : '' }}"
           placeholder="Remarques éventuelles">
    @error('notes')<span class="field-error">{{ $message }}</span>@enderror
</label>
