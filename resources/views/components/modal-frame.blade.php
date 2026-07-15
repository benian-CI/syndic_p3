@props([
    'title',
    'subtitle' => null,
    'back',
])

<turbo-frame id="modal">
    <div class="modal-backdrop">
        <div class="modal-container">
            <a href="{{ $back }}" class="modal-back" data-turbo-frame="_top">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Retour
            </a>
            <div class="modal-head">
                <div class="modal-icon">{{ $icon }}</div>
                <div class="modal-titles">
                    <h1>{{ $title }}</h1>
                    @if($subtitle)
                        <p>{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            {{ $slot }}
        </div>
    </div>
</turbo-frame>
