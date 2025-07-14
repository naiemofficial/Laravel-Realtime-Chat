

<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="shrink-0">
                    <a href="{{ url('/') }}">
                        <img class="size-8" src="{{ url("/assets/icons/logo.png")  }}" alt="Laravel Livewire Experiment">
                    </a>
                </div>
                <div class="ml-10">
                    <div class="flex items-center space-x-4">
                        <!-- Github -->
                        <x-nav-item target="_blank" href="https://github.com/naiemofficial/Websocket-with-Laravel" class="inline-flex items-center justify-center h-[36px] w-[36px] bg-gray-700 text-white rounded-md text-xl transition duration-100 hover:bg-gray-900">
                            <i class="fab fa-github"></i>
                        </x-nav-item>

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
