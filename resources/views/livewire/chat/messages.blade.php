<div class="h-full">
    <div class="w-full text-center">
        <span wire:loading class="fixed h-[25px] w-[25px] inline-flex items-center justify-center bg-white border-gray-200 border-1 border-solid rounded-[50%] z-1" style="transform: translateY(-50%)">
            <i class="inline-flex fas fa-circle-notch fa-spin text-blue-600"></i>
        </span>
    </div>

    <div class="h-full flex flex-col relative p-8 overflow-auto">
        @if(!empty($messages))
            <livewire:chat.not-found :trash="$trash" wire:key="{{ rand(1, 1000) }}" />
        @else
            <livewire:chat.messages-not-available />
        @endif
    </div>
</div>
