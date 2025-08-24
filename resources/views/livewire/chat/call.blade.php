<div x-data>
    @if($sendingCall || $incomingCall)
    <div
        x-show="$wire.sendingCall || $wire.incomingCall"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-4"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-4"
        class="relative flex items-center justify-between max-w-md w-full bg-gray-100 shadow-sm rounded-md px-4 py-2 border border-gray-200"
        id="call"
        x-init="init_Call($wire, @js(['status' => $Call?->status]), @js($settings))"
    >
        <!-- Caller Info -->
        <div class="flex items-center gap-x-3 pr-3 min-w-[200px]">
            <div class="inline-flex relative">
                <span class="inline-flex items-center justify-center h-[35px] w-[35px] min-w-[35px] border border-double border-gray-200 rounded-full text-gray-300 bg-gray-50 text-sm">
                    <i class="fa-duotone fa-solid fa-user"></i>
                </span>
            </div>
            <div class="inline-flex flex-col gap-y-1 text-xs">
                <div class="font-semibold text-gray-800 leading-[1]">{{ $peerUser?->name }}</div>
                <div class="inline-flex flex-row gap-1.5 text-gray-500 leading-[1]" style="zoom: 0.9;">
                    @if($peerSettings?->mic === false || $peerSettings?->camera === false)
                        <div class="inline-flex flex-row gap-1.5">
                            @if($peerSettings?->mic === false)<i class="fa-solid fa-microphone-slash text-red-500"></i>@endif
                            @if($peerSettings?->camera === false)<i class="fa-solid fa-video-slash text-red-500"></i>@endif
                        </div>
                    @endif
                    <div id="call-text">
                        @if($Call?->status === 'accepted')
                            <time class="min-w-9" wire:ignore></time>
                        @elseif($Call?->status === 'pending')
                            {{ $callText }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Call Actions -->
        <div class="flex items-center gap-x-3">
            @if($Call?->status === 'accepted')
                <button
                    class="px-3 py-1.5 text-sm rounded-full active:scale-95 transition flex items-center gap-1"
                    @disabled(in_array($Call?->status, ['cancelled', 'declined', 'ended']))
                    :class="!$wire.settings.mic ? 'bg-red-100 hover:bg-red-200' : 'bg-gray-200 hover:bg-gray-300'"
                    wire:click="muteUnmute"
                >
                    <i class="fa-solid" :class="!$wire.settings.mic ? 'fa-microphone-slash text-red-500' : 'fa-microphone text-gray-700'"></i>
                </button>
            @endif

            @if($Call?->type === 'video')
                @if($sendingCall || ($incomingCall && $Call?->status === 'accepted'))
                    <button
                        class="px-3 py-1.5 text-sm rounded-full active:scale-95 transition flex items-center gap-1"
                        @disabled(in_array($Call?->status, ['cancelled', 'declined', 'ended']))
                        :class="!$wire.settings.camera ? 'bg-red-100 hover:bg-red-200' : 'bg-gray-200 hover:bg-gray-300'"
                        wire:click="cameraOnOff"
                    >
                        <i class="fa-solid" :class="!$wire.settings.camera ? 'fa-video-slash text-red-500' : 'fa-video text-gray-700'"></i>
                    </button>
                @endif
            @endif

            @if($incomingCall && $Call?->status === 'pending')
                <div class="relative flex items-center justify-center">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75 animate-ping"></span>
                    <button
                        class="relative px-2.5 py-1 text-sm rounded-full bg-green-500 text-white hover:bg-green-600 active:scale-95 transition"
                        wire:click="receiveCall"
                    >
                        <i class="fa-solid {{ $Call?->type === 'video' ? 'fa-video' : 'fa-phone' }}"></i>
                    </button>
                </div>
            @endif

            <button class="px-2.5 py-1 text-sm rounded-full bg-red-500 text-white hover:bg-red-600 active:scale-95 transition"
                    @disabled(in_array($Call?->status, ['cancelled', 'declined', 'ended']))
                    wire:click="cancelDeclineEndCall"
            >
                <i class="fa-solid fa-phone" :class="$wire.incomingCall ? '-slash' : ''"></i>
            </button>
        </div>
    </div>
    @endif
</div>
