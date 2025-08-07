<div class="">
    <div class="ml-4 flex items-center md:ml-6">
        <div class="relative ml-3">
            <div class="flex flex-row items-center gap-[5px]">
                <div wire:loading wire:target="submit" class="absolute -left-5">
                    <i class="inline-flex fas fa-circle-notch fa-spin text-[#fff]"></i>
                </div>
                <div class="min-w-[300px] flex items-center border rounded-lg px-3 py-1 pr-[6px] bg-gray-900 border-gray-900 duration-300 ease-in-out {{ $isValidGuest ? 'shadow-[0_0_0_2px_#165dfb75]' : 'focus-within:shadow-[0_0_0_2px_#165dfb75]' }}">
                    <i class="min-w-[20px] fas {{ ($isValidGuest ? 'fa-user-pen' : 'fa-user') }} text-gray-400 mr-2"></i>

                    <input
                        wire:model="name"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        type="text" class="flex-1 bg-transparent text-white placeholder-gray-500 focus:outline-none focus:ring-0 py-1 text-sm disabled:text-gray-500 border-0"
                        placeholder="Your Name"
                    >
                    <button
                        type="submit"
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        class="min-w-[80px] ml-2 bg-blue-600 text-white px-4 py-1 rounded-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm cursor-pointer disabled:cursor-not-allowed"
                    >
                        {{ ($isValidGuest ? 'Update' : 'Submit') }}
                    </button>
                </div>
            </div>

            @if($isValidGuest)
                <span id="copy-guest-id" title="Copy to clipboard" class="min-w-[80px] cursor-pointer absolute bg-gray-800 text-white text-xs px-2 py-1 rounded-sm left-1/2 -translate-x-1/2 mt-1">{{ $currentGuest->uid }} <i class="fa-duotone fa-light fa-copy pointer-events-none"></i></span>
            @endif
        </div>
    </div>


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
</div>
