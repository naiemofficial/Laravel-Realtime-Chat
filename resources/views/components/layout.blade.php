<x-body class="flex flex-col h-screen min-w-[1024px]">
    <x-header>
        <x-slot:headerRight>{{ $headerRight ?? '' }}</x-slot:headerRight>
    </x-header>

    <main class="flex-1 overflow-hidden">
        <div class="h-full mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>
</x-body>
