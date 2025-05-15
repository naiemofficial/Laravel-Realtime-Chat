@php
    $wrapper    = (!empty($template['wrapper']) && $template['wrapper'] == true);
    $kbc        = (!empty($template['key-based-color']) && $template['key-based-color'] == true);
    $classes    = isset($template['class']) ? $template['class'] : '';

    $hasLineClamp = strpos($classes, 'line-clamp') !== false;
    $escapedHtmlMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
@endphp

@if($wrapper)
    <div
        x-data="{ show: false, element: $el }"
        x-init="
            setTimeout(() => show = true, 100 + {{ $delay ?? 0 }});
            setTimeout(() => show = false, 3100 + {{ $delay ?? 0 }});
            setTimeout(() => element.remove(), 3500 + {{ $delay ?? 0 }});
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-4"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-400 opacity-0 transform translate-y-4"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"

        class="
        {{ $classes }}
        {{ $kbc ? 'text-['. $text .'] border-['. $border .'] bg-['. $background .']' : '' }}
        {{ $hasLineClamp ? 'webkit-inline-box' : '' }}
        "
        title="{{ $hasLineClamp ? $escapedHtmlMessage : '' }}"
    >
@endif
    {{ $message }}
@if($wrapper) </div> @endif
