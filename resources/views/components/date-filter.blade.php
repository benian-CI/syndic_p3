@props(['action', 'label' => 'Rechercher par date'])

<form method="GET" action="{{ $action }}" class="filter-bar">
    <label>{{ $label }}
        <input type="date" name="date_debut" value="{{ request('date_debut') }}">
    </label>
    <label>Date fin
        <input type="date" name="date_fin" value="{{ request('date_fin') }}">
    </label>
    <div class="filter-actions">
        <button class="btn">Rechercher</button>
        <a class="btn secondary" href="{{ $action }}">Effacer</a>
    </div>
</form>
