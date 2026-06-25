<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de solde - Villa {{ $selectedVilla->number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #222222;
            font-size: 12px;
            margin: 28px 36px 70px;
        }
        .header {
            border-bottom: 3px solid #1a2744;
            padding-bottom: 12px;
            margin-bottom: 24px;
            text-align: center;
        }
        .date-line {
            text-align: right;
            margin-bottom: 22px;
        }
        h1 {
            color: #1a2744;
            font-size: 22px;
            margin: 0 0 18px;
        }
        .meta {
            border: 1px solid #d1d9e6;
            background: #f8fafc;
            padding: 12px 14px;
            margin-bottom: 20px;
            line-height: 1.7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        th {
            background: #1a2744;
            color: #ffffff;
            border: 1px solid #1a2744;
            padding: 7px;
            text-align: left;
            font-size: 11px;
        }
        td {
            border: 1px solid #d1d9e6;
            padding: 7px;
            font-size: 11px;
        }
        tr:nth-child(even) td {
            background: #f4f6fa;
        }
        .amount {
            text-align: right;
            white-space: nowrap;
        }
        .total {
            color: #1a2744;
            font-size: 13px;
            font-weight: bold;
            margin-top: 16px;
            text-align: right;
        }
        .signature {
            margin-top: 44px;
            text-align: right;
            color: #1a2744;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 12px;
            left: 36px;
            right: 36px;
            border-top: 1px solid #1a2744;
            color: #555555;
            font-size: 9px;
            padding-top: 6px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $logoData = base64_encode(file_get_contents(public_path('assets/logo-syndic.png')));
        $logoSrc  = 'data:image/png;base64,' . $logoData;
    @endphp
    <div class="header">
        <img src="{{ $logoSrc }}" alt="Logo" style="max-width: 300px; width: 300px; display: block; margin: 0 auto 8px auto;" />
    </div>

    <div class="date-line">Abidjan, le {{ now()->isoFormat('D MMMM Y') }}</div>

    <h1>Bulletin de solde</h1>

    <div class="meta">
        <div><strong>Villa :</strong> {{ $selectedVilla->number }} - {{ optional($selectedVilla->street)->name ?: '-' }}</div>
        <div><strong>Propriétaire :</strong> {{ $selectedVilla->owner_name }}</div>
        <div><strong>Période :</strong> {{ $start->format('d/m/Y') }} au {{ $end->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 16%;">Date</th>
                <th style="width: 14%;">Mois</th>
                <th style="width: 18%; text-align: right;">Montant</th>
                <th style="width: 24%;">Méthode</th>
                <th style="width: 28%;">Référence</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contributions as $contribution)
                <tr>
                    <td>{{ $contribution->paid_at->format('d/m/Y') }}</td>
                    <td>{{ $contribution->month->format('m/Y') }}</td>
                    <td class="amount">{{ number_format($contribution->amount, 0, ',', ' ') }} F CFA</td>
                    <td>{{ $contribution->payment_method ?: '-' }}</td>
                    <td>{{ $contribution->reference ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #666666;">
                        Aucune cotisation trouvée pour cette période.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">Total : {{ number_format($total, 0, ',', ' ') }} F CFA</div>

    @php
        $sigData = base64_encode(file_get_contents(public_path('assets/signature.png')));
        $sigSrc  = 'data:image/png;base64,' . $sigData;
    @endphp
    <div class="signature">
        <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px; font-size: 11px;">
            PO/Le PRESIDENT
        </p>
        <p style="font-weight: normal; font-size: 10px; margin-bottom: 6px;">
            P/Le Secrétaire Permanent
        </p>
        <img src="{{ $sigSrc }}" alt="Signature" style="width: 130px; display: block; margin: 0 0 4px auto;" />
        <p style="font-weight: bold; font-size: 11px; margin-top: 2px;">Wilfried SERY</p>
    </div>

    <div class="footer">
        B.P. 274 cidex 03 Abidjan &nbsp;|&nbsp; syndicrgp3@yahoo.fr &nbsp;|&nbsp; N° Compte Contribuable : 0308872 V &nbsp;|&nbsp; Tél. : 22 49 14 04
    </div>
</body>
</html>
