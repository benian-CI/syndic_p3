@php
    $sigData = base64_encode(file_get_contents(public_path('assets/signature.png')));
    $sigSrc  = 'data:image/png;base64,' . $sigData;
@endphp
<div style="margin-top: 48px; text-align: right; padding-right: 4px;">
    <p style="font-size: 11px; font-weight: bold; text-decoration: underline;
              color: #1a2744; margin-bottom: 2px; letter-spacing: 0.3px;">
        PO/Le PRESIDENT
    </p>
    <p style="font-size: 10px; color: #1a2744; margin-bottom: 6px;">
        P/Le Secr&eacute;taire Permanent
    </p>
    <img src="{{ $sigSrc }}"
         style="width: 130px; display: block; margin: 0 0 4px auto;" />
    <p style="font-size: 11px; font-weight: bold; color: #1a2744; margin-top: 2px;">
        Wilfried SERY
    </p>
</div>
