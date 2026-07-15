@props([
    'title',
    'subtitle' => null,
    'back',
])

@php $isFrame = request()->header('Turbo-Frame') === 'modal'; @endphp

@if($isFrame)
    <x-modal-frame :title="$title" :subtitle="$subtitle" :back="$back">
        <x-slot:icon>{{ $icon }}</x-slot:icon>
        {{ $slot }}
    </x-modal-frame>
@else
    <x-layouts.app :title="$title">
        <x-modal-frame :title="$title" :subtitle="$subtitle" :back="$back">
            <x-slot:icon>{{ $icon }}</x-slot:icon>
            {{ $slot }}
        </x-modal-frame>
    </x-layouts.app>
@endif
