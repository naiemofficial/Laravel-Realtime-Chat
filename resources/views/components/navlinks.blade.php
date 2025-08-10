

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
                        <x-nav-item target="_blank" href="https://github.com/naiemofficial/Websocket-with-Laravel" class="inline-flex items-center justify-center h-[36px] w-[36px] bg-gray-700 text-white rounded-md text-xl transition duration-100 hover:bg-gray-900 m-0">
                            <i class="fab fa-github"></i>
                        </x-nav-item>

                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        @foreach($menus as $link => $name)
                            <x-nav-item href="{{ $link }}">{{ $name }}</x-nav-item>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex-col justify-center relative">
                <x-usernav />
                @if(auth()->check())
                    <span id="copy-guest-id" title="Copy to clipboard" class="min-w-[90px] cursor-pointer absolute left-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded-sm mt-0.5" style="transform: translateX(-50%)">{{ auth()->user()->uid }} <i class="fa-duotone fa-light fa-copy pointer-events-none"></i></span>
                @endif
            </div>

        </div>
    </div>
</nav>

<script>
    document.addEventListener('click', function (event) {
        const element = event.target;
        if (element.id === 'copy-guest-id') {
            const guestId = element.closest('span').textContent.trim();
            navigator.clipboard.writeText(guestId).then(() => {
                const icon = element.querySelector('i');
                if (icon) {
                    const originalClass = icon.className;
                    icon.className = 'fa fa-check-circle';
                    setTimeout(() => {
                        icon.className = originalClass;
                    }, 2000);
                }
            }).catch(err => {
                console.error('Failed to copy:', err);
            });
        }
    });
</script>
