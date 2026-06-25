@php
    $announcement = $announcement ?? null;
@endphp

<input type="hidden" name="target" value="all">

<label>Destinataire
    <input name="destinataire" value="{{ old('destinataire', $announcement?->destinataire) }}"
           class="{{ $errors->has('destinataire') ? 'is-invalid' : '' }}"
           placeholder="Ex : Tous les propriétaires" required>
    @error('destinataire')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label>Objet
    <input name="title" value="{{ old('title', $announcement?->title) }}"
           class="{{ $errors->has('title') ? 'is-invalid' : '' }}"
           placeholder="Ex : Réunion du quartier" required>
    @error('title')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label>Canal principal
    <select name="channel" class="{{ $errors->has('channel') ? 'is-invalid' : '' }}" required>
        <option value="email" @selected(old('channel', $announcement?->channel) === 'email')>Email</option>
        <option value="phone" @selected(old('channel', $announcement?->channel) === 'phone')>Téléphone</option>
        <option value="whatsapp" @selected(old('channel', $announcement?->channel) === 'whatsapp')>WhatsApp</option>
    </select>
    @error('channel')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label class="full">Message
    <textarea name="message"
              class="{{ $errors->has('message') ? 'is-invalid' : '' }}"
              placeholder="Rédigez votre message ici..."
              required>{{ old('message', $announcement?->message) }}</textarea>
    @error('message')<span class="field-error">{{ $message }}</span>@enderror
</label>
