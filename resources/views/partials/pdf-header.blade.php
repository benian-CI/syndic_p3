<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #222222;
        }
        .page {
            padding: 28px 36px 70px 36px;
        }
        .assoc-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a2744;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .assoc-sub {
            font-size: 10px;
            color: #c8820a;
            margin-top: 3px;
        }
        .divider-thick {
            border: none;
            border-top: 3px solid #1a2744;
            margin: 10px 0 3px 0;
        }
        .divider-thin {
            border: none;
            border-top: 1px solid #a0aec0;
            margin-bottom: 22px;
        }
        .dest-line {
            font-size: 11px;
            margin-bottom: 10px;
        }
        .dest-label {
            font-weight: bold;
            text-decoration: underline;
        }
        .objet-line {
            font-size: 11px;
            margin-bottom: 24px;
        }
        .objet-label {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- EN-TÊTE : image officielle de l'association --}}
    @php
        $logoData = base64_encode(file_get_contents(public_path('assets/logo-syndic.png')));
        $logoSrc  = 'data:image/png;base64,' . $logoData;
    @endphp
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 6px;">
        <tr>
            <td style="text-align: center; padding-bottom: 8px;">
                <img src="{{ $logoSrc }}"
                     style="max-width: 300px; width: 300px; display: block; margin: 0 auto;" />
            </td>
        </tr>
    </table>

    {{-- Double séparateur --}}
    <hr class="divider-thick">
    <hr class="divider-thin">

    {{-- Ville + Date (alignée à droite) --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 28px;">
        <tr>
            <td></td>
            <td style="text-align: right; font-size: 11px; color: #333333; white-space: nowrap;">
                Abidjan, le {{ $date ?? now()->isoFormat('D MMMM Y') }}
            </td>
        </tr>
    </table>

    {{-- Dest. --}}
    @isset($dest)
    <div class="dest-line">
        <span class="dest-label">Dest.</span>&nbsp;: {{ $dest }}
    </div>
    @endisset

    {{-- Objet --}}
    @isset($objet)
    <div class="objet-line">
        <span class="objet-label">Objet</span>&nbsp;: {{ $objet }}
    </div>
    @endisset
