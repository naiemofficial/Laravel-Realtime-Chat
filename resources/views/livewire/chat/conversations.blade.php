<div class="h-full">
    <livewire:chat.conversation-alert />

    <livewire:chat.add-conversation />

    @if($conversations->count() < 1)
        <livewire:chat.conversation-not-available />
    @else
        <ul role="list" class="divide-y divide-gray-100 space-y-1 mt-2">
            @foreach($conversations as $index => $conversation)
                <li
                    wire:key="{{ $conversation->id }}"
                    wire:click="openConversation({{ $conversation->id }})"
                    x-data="{ show: false }"
                    x-init="setTimeout(() => show = true, {{ $index * 80 }})"
                    x-show="show"
                    x-transition.duration.300ms
                    @class([
                        'flex justify-between gap-x-6 px-3 py-3 transition-[0.3s] bg-white rounded-sm cursor-pointer',
                        'ring-2 ring-blue-400' => $openedConversation === $conversation->id,
                    ])
                    style="transition: 0.3s"
                    aria-selected="{{ $openedConversation === $conversation->id ? 'true' : 'false' }}"
                >
                    <div class="flex min-w-0 gap-x-4">
                            <span class="inline-flex items-center justify-center h-[48px] w-[48px] min-w-[48px] border border-double border-gray-200 rounded-sm text-gray-300 bg-gray-100 text-xl">
                                <i class="fa-duotone fa-solid fa-user"></i>
                            </span>
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold text-gray-900">{{ $conversation->recipient($currentGuest->id)->name }}</p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500">{{ $conversation->recipient($currentGuest->id)->uid }}</p>
                        </div>
                    </div>

                    <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                        <div class="inline-flex items-center space-x-3 justify-center text-xs/5 text-gray-500">
                            <span title="updated"><i class="fa-light fa-clock mr-1"></i> {{ $conversation->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <x-pagination :data="$conversations" class="inner-col-pagination left-0 rounded-br-none rounded-bl-md"/>
</div>
