<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Arriérés de paiement - Villa {{ $selectedVilla->number }}</title>
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
            padding: 8px;
            text-align: left;
        }
        td {
            border: 1px solid #d1d9e6;
            padding: 8px;
        }
        tr:nth-child(even) td {
            background: #fff5f5;
        }
        .status {
            color: #c0392b;
            font-weight: bold;
        }
        .total {
            color: #c0392b;
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

    <h1>Arriérés de paiement</h1>

    <div class="meta">
        <div><strong>Villa :</strong> {{ $selectedVilla->number }} - {{ optional($selectedVilla->street)->name ?: '-' }}</div>
        <div><strong>Propriétaire :</strong> {{ $selectedVilla->owner_name }}</div>
        <div><strong>Période :</strong> {{ $start->format('d/m/Y') }} au {{ $end->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 55%;">Mois</th>
                <th style="width: 45%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($arrears as $arrear)
                <tr>
                    <td>{{ $arrear['month']->translatedFormat('F Y') }}</td>
                    <td class="status">Non payé</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #666666;">
                        Aucun arriéré trouvé pour cette période.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">Total des mois impayés : {{ $arrears->count() }}</div>

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
