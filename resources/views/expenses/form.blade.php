<label>Titre
    <input name="title"
           value="{{ old('title', $expense->title ?? '') }}"
           class="{{ $errors->has('title') ? 'is-invalid' : '' }}"
           placeholder="Ex : Nettoyage des allées" required>
    @error('title')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Montant
    <input type="number" step="0.01" min="0" name="amount"
           value="{{ old('amount', $expense->amount ?? '') }}"
           class="{{ $errors->has('amount') ? 'is-invalid' : '' }}"
           placeholder="0.00" required>
    @error('amount')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Date
    <input type="date" name="spent_at"
           value="{{ old('spent_at', isset($expense) ? $expense->spent_at->format('Y-m-d') : now()->format('Y-m-d')) }}"
           class="{{ $errors->has('spent_at') ? 'is-invalid' : '' }}"
           required>
    @error('spent_at')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label>Catégorie
    <input name="category"
           value="{{ old('category', $expense->category ?? '') }}"
           class="{{ $errors->has('category') ? 'is-invalid' : '' }}"
           placeholder="Sécurité, nettoyage, travaux...">
    @error('category')<span class="field-error">{{ $message }}</span>@enderror
</label>
<label class="full">Description
    <textarea name="description"
              class="{{ $errors->has('description') ? 'is-invalid' : '' }}"
              placeholder="Détails de la dépense...">{{ old('description', $expense->description ?? '') }}</textarea>
    @error('description')<span class="field-error">{{ $message }}</span>@enderror
</label>
