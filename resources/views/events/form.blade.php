<label>Choisissez une option
    <select name="title" id="choix-liste" class="{{ $errors->has('title') ? 'is-invalid' : '' }}" required>
        <option value="">Choisir</option>
        <option value="Terrain" @selected(old('title', $event->title ?? '') === 'Terrain')>Terrain</option>
        <option value="Panneau publicitaire" @selected(old('title', $event->title ?? '') === 'Panneau publicitaire')>Panneau publicitaire</option>
        <option value="Antenne" @selected(old('title', $event->title ?? '') === 'Antenne')>Antenne</option>
    </select>
    @error('title')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label>Mois de cotisation
    <input type="month" name="month"
           value="{{ old('month', isset($event) && $event->month ? $event->month->format('Y-m') : now()->format('Y-m')) }}"
           class="{{ $errors->has('month') ? 'is-invalid' : '' }}"
           required>
    @error('month')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label>Montant
    <input type="number" step="0.01" min="0" name="amount"
           value="{{ old('amount', $event->amount ?? '') }}"
           class="{{ $errors->has('amount') ? 'is-invalid' : '' }}"
           placeholder="0.00" required>
    @error('amount')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label>Date de paiement
    <input type="date" name="paid_at"
           value="{{ old('paid_at', isset($event) && $event->paid_at ? $event->paid_at->format('Y-m-d') : now()->format('Y-m-d')) }}"
           class="{{ $errors->has('paid_at') ? 'is-invalid' : '' }}"
           required>
    @error('paid_at')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label>Méthode de paiement
    <select name="payment_method" class="{{ $errors->has('payment_method') ? 'is-invalid' : '' }}">
        <option value="">Choisir une méthode</option>
        @foreach (['Espèce', 'Mobile money', 'Banque'] as $method)
            <option value="{{ $method }}" @selected(old('payment_method', isset($event) ? $event->payment_method : '') === $method)>{{ $method }}</option>
        @endforeach
    </select>
    @error('payment_method')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label>Reference
    <input name="reference"
           value="{{ old('reference', $event->reference ?? '') }}"
           class="{{ $errors->has('reference') ? 'is-invalid' : '' }}"
           placeholder="Numero de transaction (optionnel)">
    @error('reference')<span class="field-error">{{ $message }}</span>@enderror
</label>

<label class="full">Description
    <textarea name="description"
              class="{{ $errors->has('description') ? 'is-invalid' : '' }}"
              placeholder="Details de la ressource...">{{ old('description', $event->description ?? '') }}</textarea>
    @error('description')<span class="field-error">{{ $message }}</span>@enderror
</label>
