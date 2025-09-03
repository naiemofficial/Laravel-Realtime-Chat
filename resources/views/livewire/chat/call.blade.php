<div x-data>
    @if($sendingCall || $incomingCall)
    <div
        x-show="$wire.sendingCall || $wire.incomingCall"
        id="call"
        wire:key="{{$Call->id}}"
        x-init="init_Call($wire, @js($sendingCall), @js($incomingCall), @js(['status' => $Call?->status, 'type' => $Call?->type]), @js($settings), @js($peerSettings))"
    >

        @if($Call->type === 'video' && ($sendingCall || ($incomingCall && $Call->status === 'accepted')))
            <!-- Video Feeds -->
            <div
                wire:ignore
                class="z-30"
                id="video-feed"
                x-data
                x-init="setVideoFeedPosition($el)"
            >
                <div data-aria="video-feed" class="relative max-w-lg w-full bg-gray-300 shadow-lg rounded-md overflow-hidden border border-gray-300 p-1.5">
                    <div class="relative overflow-hidden flex justify-between items-center rounded-md bg-gray-200 gap-2">
                        <!-- Peer Video -->
                        <div class="z-10 flex-1 rounded-md overflow-hidden relative transition-all duration-300">
                            <div class="video-call-overlay overflow-hidden rounded-md z-10 flex justify-center items-center absolute top-0 left-0 h-full w-full object-cover bg-cover bg-center">
                                <span class="user-image z-[21] absolute top-1/2 left-1/2 inline-flex items-center justify-center h-[50px] w-[50px] min-w-[35px] border border-double border-gray-700 rounded-full text-gray-500 bg-gray-800 text-lg" style="transform: translate(-50%, -50%); zoom: 0.55;">
                                    <i class="fa-duotone fa-solid fa-user"></i>
                                </span>
                                <i class="buffering loading fa-solid fa-circle-notch fa-spin text-white z-[22]" style="zoom: 0.7"></i>
                            </div>
                            <video autoplay playsinline x-ref="peer" class="w-full h-full max-w-[300px] min-w-[300px] max-h-[165px] min-h-[165px] bg-gray-900 object-cover" style="transform: scaleX(-1)"></video>
                        </div>

                        <!-- Local Video -->
                        <div class="z-20 absolute right-1.5 top-1.5 w-auto h-14 bg-gray-800 rounded-md overflow-hidden border-none border-gray-600 transition-all duration-300">
                            <div class="video-call-overlay overflow-hidden rounded-md z-10 flex justify-center items-center absolute top-0 left-0 h-full w-full object-cover bg-cover bg-center">
                                <span class="user-image z-[21] absolute top-1/2 left-1/2 inline-flex items-center justify-center h-[50px] w-[50px] min-w-[35px] border border-double border-gray-700 rounded-full text-gray-500 bg-gray-800 text-lg" style="transform: translate(-50%, -50%); zoom: 0.55;">
                                    <i class="fa-duotone fa-solid fa-user"></i>
                                </span>
                                <i class="buffering loading fa-solid fa-circle-notch fa-spin text-white z-[22]" style="zoom: 0.7"></i>
                            </div>
                            <video autoplay muted playsinline x-ref="local" class="w-full h-full object-cover transform" style="transform: scaleX(-1)"></video>
                        </div>
                    </div>
                </div>
            </div>
        @endif



        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-4"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-4"
        >


            <!-- Caller Info -->
            <div class="relative flex flex-col gap-2 max-w-md w-full bg-gray-100 shadow-sm rounded-md px-4 py-2 border border-gray-200">
                <div class="flex flex-row items-center justify-between gap-2">
                    <div class="flex items-center gap-x-3 pr-3 min-w-[150px]">
                        @if($Call->type === 'voice')
                            <div class="inline-flex relative">
                                <span class="inline-flex items-center justify-center h-[35px] w-[35px] min-w-[35px] border border-double border-gray-200 rounded-full text-gray-300 bg-gray-50 text-sm">
                                    <i class="fa-duotone fa-solid fa-user"></i>
                                </span>
                            </div>
                        @endif
                        <div class="inline-flex flex-col gap-y-1 text-xs">
                            <div class="font-semibold text-gray-800 leading-[1]">{{ $peerUser?->name }}</div>
                            <div class="inline-flex flex-row gap-1.5 text-gray-500 leading-[1]" style="zoom: 0.9;">
                                @if($peerSettings?->mic === false || $peerSettings?->camera === false)
                                    <div class="inline-flex flex-row gap-1.5">
                                        @if($peerSettings?->mic === false)<i class="fa-solid fa-microphone-slash text-red-500"></i>@endif
                                        @if($peerSettings?->camera === false) <i class="fa-solid fa-video-slash text-red-500"></i>@endif
                                    </div>
                                @endif
                                <div id="call-text">
                                    @if($Call?->status === 'accepted')
                                        <time class="min-w-9" wire:ignore></time>
                                    @elseif($Call?->status === 'pending')
                                        @if($sendingCall === true && $peerSettings->ringing === true)
                                           Ringing
                                        @else
                                            {{ $callText }}
                                        @endif
                                        <span class="ping" wire:ignore></span>
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
                                :class="!$wire.settings?.mic ? 'bg-red-100 hover:bg-red-200' : 'bg-gray-200 hover:bg-gray-300'"
                                wire:click="muteUnmute"
                            >
                                <i class="fa-solid" :class="!$wire.settings?.mic ? 'fa-microphone-slash text-red-500' : 'fa-microphone text-gray-700'"></i>
                            </button>
                        @endif

                        @if($Call?->type === 'video')
                            @if($sendingCall || ($incomingCall && $Call?->status === 'accepted'))
                                <button
                                    class="px-3 py-1.5 text-sm rounded-full active:scale-95 transition flex items-center gap-1"
                                    @disabled(in_array($Call?->status, ['cancelled', 'declined', 'ended']))
                                    :class="!$wire.settings?.camera ? 'bg-red-100 hover:bg-red-200' : 'bg-gray-200 hover:bg-gray-300'"
                                    wire:click="cameraOnOff"
                                >
                                    <i class="fa-solid" :class="!$wire.settings?.camera ? 'fa-video-slash text-red-500' : 'fa-video text-gray-700'"></i>
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

                @if($Call->type === 'voice')
                    <div wire:ignore class="flex relative hidden">
                        <audio autoplay muted playsinline class="w-full h-[20px] absolute -bottom-[6px]" style="zoom: 0.55">
                            <source src="https://www.w3schools.com/tags/horse.mp3" type="audio/mpeg">
                        </audio>
                    </div>
                @endif
            </div>

        </div>
    </div>
    @endif
</div>
