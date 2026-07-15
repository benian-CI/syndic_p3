@props([
    'title',
    'subtitle' => null,
    'back',
])

<turbo-frame id="modal">
    <div class="modal-backdrop">
        <div class="modal-container">
            <div class="modal-head">
                <div class="modal-icon">{{ $icon }}</div>
                <div class="modal-titles">
                    <h1>{{ $title }}</h1>
                    @if($subtitle)
                        <p>{{ $subtitle }}</p>
                    @endif
                </div>
                <a href="{{ $back }}" class="modal-close" data-turbo-frame="_top" aria-label="Fermer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </a>
            </div>

            {{ $slot }}
        </div>
    </div>
</turbo-frame>
