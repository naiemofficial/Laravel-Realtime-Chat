@php
use \Carbon\Carbon;
@endphp

<div class="h-full">
    <livewire:chat.message-alert />

    <div class="w-full text-center">
        <span wire:loading class="fixed h-[25px] w-[25px] inline-flex items-center justify-center bg-white border-gray-200 border-1 border-solid rounded-[50%] z-1" style="transform: translateY(-50%)">
            <i class="inline-flex fas fa-circle-notch fa-spin text-blue-600"></i>
        </span>
    </div>

    <div
    wire:loading.class="opacity-0"
    class="h-full flex flex-col relative">
        @if($conversationSelected)
            <div class="conversation-header flex items-center justify-between gap-3 p-3 px-8 sticky top-0 bg-gray-100 shadow-sm z-10">
                <div class="inline-flex items-center gap">
                    <div class="w-10 h-10 bg-white rounded-full inline-flex items-center justify-center text-gray-300">
                        <i class="fa-duotone fa-solid fa-user"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800 text-sm">{{ $recipient->name }}</span>
                        <span class="text-[0.65rem] text-gray-600">{{ $recipient->uid }}</span>
                    </div>
                </div>
                <livewire:chat.call :conversation-id="$conversationId" :recipient-id="$recipient->id" />
            </div>
        @endif

        @if(empty($messages))
            <livewire:chat.messages-not-available :conversationSelected="$conversationSelected" wire:key="{{ rand(1, 1000) }}" />
        @else
            <div class="flex flex-col mt-auto overflow-y-auto overflow-x-hidden">
                <ul
                role="list" id="chat-box" data-conversation="{{ $conversationId }}"
                class="divide-y divide-gray-100 space-y-1"
                >
                    @php
                        $last_user_id = 0;
                    @endphp
                    @foreach($messages as $index => $message)
                        @php
                            $messageUser = $Conversation->participant($message->participant_id)->user(); // User as participant
                        @endphp
                        <li
                        wire:key="{{ $message->id }}"
                        x-data="{ show: false }"
                        x-init="revealAndScroll({{count($messages)}}, {{ $index }}, $el, {{ 100 }})"
                        x-show="show"
                        x-transition.duration.300ms
                        class="px-8 pt-3 transition-[0.3s] bg-white border-none"
                        style="transition: 0.3s"
                        data-type="{{ $message->type }}"
                        @if($message->type !== 'starter')
                            data-participant="{{ (auth()->user()->id == $messageUser->id) ? "self" : "recipient" }}"
                        @endif
                    >
                        @if($message->type === 'starter')

                            <div class="text-center text-gray-500 text-xs mt-4 mb-10 w-full">
                                @if(auth()->user()->id == $messageUser->id)
                                    You {{ $message->text }} with {{ $recipient->name }}
                                @else
                                    {{ $messageUser->name }} {{ $message->text }} with you
                                @endif
                                <br class="mt-1">
                                <span class="text-gray-400 text-[10px]">
                                    on {{ Carbon::parse($message->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        @else
                            <div class="block">
                                <div class="inline-block relative group">
                                    @if($message->type === 'regular')
                                        <div class="inline-block bg-blue-500 text-white rounded-lg px-4 py-2 max-w-xs">
                                            <p class="text-sm leading-snug">
                                                {{ $message->text }}
                                            </p>
                                        </div>
                                    @elseif($message->type === 'call')
                                        <div class="inline-block bg-gray-100 border border-gray-200 text-gray-700 rounded-lg px-4 py-3 max-w-xs">
                                            <p class="text-xs leading-snug font-medium capitalize">
                                                @if($MessageInstance->call($message->id)))
                                                    <i class="fa-solid fa-{{ $message->text ? 'phone' : 'video' }}-arrow-{{(auth()->user()->id == $messageUser->id) ? 'up-right' : 'down-left'}} pr-1.5"></i>
                                                @else
                                                    <i class="fa-solid fa-phone-missed text-red-500"></i> Missed
                                                @endif

                                                {{ $message->text }} {{ $message->type }}
                                            </p>
                                        </div>
                                    @else
                                        <div class="inline-block border border-gray-100 rounded-full">
                                            <p class="text-sm leading-snug">
                                                {{ $message->text }}
                                            </p>
                                        </div>
                                    @endif
                                    <div class="msg-tooltip absolute bottom-full left-1/2 mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 z-10 whitespace-nowrap" style="transform: translateX(-50%)">
                                        {{ Carbon::parse($message->created_at)->format('M j, Y, g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </li>
                    @php
                        $last_user_id = $messageUser->id;
                    @endphp
                    @endforeach
                </ul>
            </div>
        @endif

        @if($conversationSelected)
            <div wire:loading.class="opacity-0" class="sticky bottom-0">
                <livewire:chat.send-message :conversation-id="$conversationId" wire:key="{{ rand(1, 1000) }}" />
            </div>
        @endif
    </div>

</div>
