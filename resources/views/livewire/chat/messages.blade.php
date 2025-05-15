@php
use \Carbon\Carbon;
@endphp

<div class="h-full">
    <div class="w-full text-center">
        <span wire:loading class="fixed h-[25px] w-[25px] inline-flex items-center justify-center bg-white border-gray-200 border-1 border-solid rounded-[50%] z-1" style="transform: translateY(-50%)">
            <i class="inline-flex fas fa-circle-notch fa-spin text-blue-600"></i>
        </span>
    </div>

    <div class="h-full flex flex-col relative p-8 pb-13 overflow-auto {{ ($conversationSelected && !empty($messages)) ? 'justify-end' : '' }}">
        @if(empty($messages))
            <livewire:chat.messages-not-available :conversationSelected="$conversationSelected" wire:key="{{ rand(1, 1000) }}" />
        @else
            <ul role="list" class="divide-y divide-gray-100 space-y-1 mt-2">
                @foreach($messages as $index => $message)
                    <li
                    wire:key="{{ $message->id }}"
                    x-data="{ show: false }"
                    x-init="setTimeout(() => show = true, {{ $index * 80 }})"
                    x-show="show"
                    x-transition.duration.300ms
                    class="flex justify-between gap-x-6 px-3 py-3 transition-[0.3s] bg-white rounded-sm"
                    style="transition: 0.3s"
                >
                    @if($message->type === 'starter')
                        <div class="text-center text-gray-500 text-xs my-4 w-full">
                            @if($currentGuest->id == $message->sender_id)
                                You {{ $message->text }} with {{ $recipient->name }}
                            @else
                                {{ $this->sender($message->sender_id)->name }} {{ $message->text }} with you
                            @endif
                            <br class="mt-1">
                            <span class="text-gray-400 text-[10px]">
                                on {{ Carbon::parse($message->created_at)->diffForHumans() }}
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-full inline-flex items-center justify-center text-gray-300"><i class="fa-duotone fa-solid fa-user"></i></div>
                            <div class="bg-blue-500 text-white rounded-lg px-4 py-2 max-w-xs">
                                <p class="text-sm leading-snug">
                                    {{ $message->text ?? 'No messages yet' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if($conversationSelected)
        <livewire:chat.send-message wire:key="{{ rand(1, 1000) }}" />
    @endif
</div>
