

<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="shrink-0">
                    <img class="size-8 w-auto" src="{{ url("/websocket.png") }}" alt="Websocket with Laravel">
                </div>
                <div class="">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        @foreach($menus as $link => $name)
                            <x-nav-item href="{{ $link }}">{{ $name }}</x-nav-item>
                        @endforeach
                    </div>
                </div>
            </div>

            <livewire:guest.form />

        </div>
    </div>
</nav>
