
@include('partials.pdf-header', [
    'title' => 'Annonce — ' . $announcement->title,
    'date'  => $announcement->sent_at
        ? $announcement->sent_at->isoFormat('D MMMM Y')
        : now()->isoFormat('D MMMM Y'),
    'dest'  => $announcement->destinataire,
    'objet' => $announcement->title,
])

<div style="font-size: 12px; line-height: 1.9; text-align: justify; color: #222222; margin-top: 20px; min-height: 160px;">
    {!! nl2br(e($announcement->message)) !!}
</div>

@if($announcement->channel)
<div style="font-size: 10px; color: #666666; margin-top: 16px;">
    Canal de diffusion&nbsp;: {{ ucfirst($announcement->channel) }}
</div>
@endif

@include('partials.pdf-signature')

@include('partials.pdf-footer')
