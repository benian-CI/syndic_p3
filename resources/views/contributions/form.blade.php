<label>Villa
    <select name="villa_id" class="{{ $errors->has('villa_id') ? 'is-invalid' : '' }}" required>
        <option value="">Choisir une villa</option>
        @foreach ($villas as $villa)
            <option value="{{ $villa->id }}" @selected(old('villa_id', isset($contribution) ? $contribution->villa_id : '') == $villa->id)>Villa {{ $villa->number }} — {{ $villa->owner_name }} ({{ $villa->street?->name ?? 'Sans rue' }})</option>
        @endforeach
    </select>
    @error('villa_id')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Mois de cotisation
    <input type="month" name="month"
           value="{{ old('month', isset($contribution) ? $contribution->month->format('Y-m') : now()->format('Y-m')) }}"
           class="{{ $errors->has('month') ? 'is-invalid' : '' }}"
           required>
    @error('month')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Montant
    <input type="number" step="0.01" min="0" name="amount"
           value="{{ old('amount', isset($contribution) ? $contribution->amount : '') }}"
           class="{{ $errors->has('amount') ? 'is-invalid' : '' }}"
           placeholder="0.00" required>
    @error('amount')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Date de paiement
    <input type="date" name="paid_at"
           value="{{ old('paid_at', isset($contribution) ? $contribution->paid_at->format('Y-m-d') : now()->format('Y-m-d')) }}"
           class="{{ $errors->has('paid_at') ? 'is-invalid' : '' }}"
           required>
    @error('paid_at')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Méthode de paiement
    <select name="payment_method" class="{{ $errors->has('payment_method') ? 'is-invalid' : '' }}">
        <option value="">Choisir une méthode</option>
        @foreach (['Espèce', 'Mobile money', 'Banque'] as $method)
            <option value="{{ $method }}" @selected(old('payment_method', isset($contribution) ? $contribution->payment_method : '') === $method)>{{ $method }}</option>
        @endforeach
    </select>
    @error('payment_method')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Référence
    <input name="reference"
           value="{{ old('reference', isset($contribution) ? $contribution->reference : '') }}"
           class="{{ $errors->has('reference') ? 'is-invalid' : '' }}"
           placeholder="N° de transaction (optionnel)">
    @error('reference')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label class="full">Notes
    <textarea name="notes"
              class="{{ $errors->has('notes') ? 'is-invalid' : '' }}"
              placeholder="Remarques éventuelles...">{{ old('notes', isset($contribution) ? $contribution->notes : '') }}</textarea>
    @error('notes')<span class="field-error">{{ $message }}</span>@enderror
</label>
