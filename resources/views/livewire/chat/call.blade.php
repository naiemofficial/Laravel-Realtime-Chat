<div x-data>
    @if($sendingCall || $incomingCall)
    <div
        wire:poll.1500ms="refresh"
        x-show="$wire.sendingCall || $wire.incomingCall"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-4"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-4"
        class="flex items-center justify-between max-w-md w-full bg-gray-100 shadow-sm rounded-md px-4 py-2 border border-gray-200"
    >
        <!-- Caller Info -->
        <div class="flex items-center gap-x-3 pr-3">
            <span class="inline-flex items-center justify-center h-[35px] w-[35px] min-w-[35px] border border-double border-gray-200 rounded-full text-gray-300 bg-gray-50 text-sm">
                <i class="fa-duotone fa-solid fa-user"></i>
            </span>
            <div class="inline-flex flex-col gap-y-1">
                <p class="text-xs font-semibold text-gray-800 leading-[1]">John Doe</p>
                <p class="text-xs text-gray-500 leading-[1]" style="zoom: 0.9;">
                    <span>{{ $callText }}</span>
                </p>
            </div>
        </div>

        <!-- Call Actions -->
        <div class="flex items-center gap-x-3">
            @if($Call?->status === 'received')
                <button
                    class="px-3 py-1.5 text-sm rounded-full active:scale-95 transition flex items-center gap-1"
                    :class="$wire.isMuted ? 'bg-red-100 hover:bg-red-200' : 'bg-gray-200 hover:bg-gray-300'"
                >
                    <i class="fa-solid"
                       :class="$wire.isMuted ? 'fa-microphone-slash text-red-500' : 'fa-microphone text-gray-700'"></i>
                </button>
            @endif
            <template x-if="$wire.incomingCall">
                <div class="relative flex items-center justify-center">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75 animate-ping"></span>
                    <button
                        class="relative px-2.5 py-1 text-sm rounded-full bg-green-500 text-white hover:bg-green-600 active:scale-95 transition"
                        wire:click=""
                    >
                        <i class="fa-solid {{ $Call?->type === 'video' ? 'fa-video' : 'fa-phone' }}"></i>
                    </button>
                </div>
            </template>

            <button class="px-2.5 py-1 text-sm rounded-full bg-red-500 text-white hover:bg-red-600 active:scale-95 transition"
                    wire:click="cancelDeclineCall"
            >
                <i class="fa-solid fa-phone" :class="$wire.incomingCall ? '-slash' : ''"></i>
            </button>
        </div>
    </div>
    @endif
</div>
