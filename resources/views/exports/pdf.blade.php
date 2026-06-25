@include('partials.pdf-header', [
    'title' => 'Export — ' . $title,
    'date'  => now()->format('d/m/Y'),
    'dest'  => 'Administration — Usage interne',
    'objet' => 'Liste des ' . $title . ' — Exportée le ' . now()->format('d/m/Y'),
])

<style>
    .data-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 8px;
        margin-top: 4px;
    }
    .data-table thead tr {
        background-color: #1a2744;
        color: #ffffff;
    }
    .data-table th {
        padding: 5px 4px;
        text-align: left;
        font-weight: bold;
        font-size: 8px;
        border: 1px solid #1a2744;
    }
    .data-table td {
        padding: 4px;
        border: 1px solid #d1d9e6;
        font-size: 7.5px;
        color: #222222;
        vertical-align: top;
    }
    .data-table tbody tr:nth-child(even) td {
        background-color: #f4f6fa;
    }
    .empty-row td {
        text-align: center;
        color: #888888;
        padding: 10px;
        font-style: italic;
    }
    .table-meta {
        font-size: 9px;
        color: #555555;
        margin-bottom: 6px;
    }
</style>

<div class="table-meta">
    Généré le {{ now()->format('d/m/Y à H:i') }} &mdash; {{ $rows->count() }} enregistrement(s)
</div>

<table class="data-table">
    <thead>
        <tr>
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ $cell ?? '-' }}</td>
                @endforeach
            </tr>
        @empty
            <tr class="empty-row">
                <td colspan="{{ count($headers) }}">Aucun enregistrement trouvé pour cette période.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@include('partials.pdf-footer')
