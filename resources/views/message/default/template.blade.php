<div x-data="{ show: false, element: $el }"
     x-init="setTimeout(() => show = true, 100 + {{ $delay ?? 0 }}); setTimeout(() => show = false, 3100 + {{ $delay ?? 0 }}); setTimeout(() => element.remove(), 3500 + {{ $delay ?? 0 }})"
     x-show="show"
     x-transition:enter="transition ease-out duration-300 opacity-0 transform -translate-x-4"
     x-transition:enter-start="opacity-0 transform -translate-x-4"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-400 opacity-0 transform translate-x-4"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-4"
     class="mt-2 p-2 border border-l-5 text-sm {{ $class ?? '' }}">
    {{ $message }}
</div>
