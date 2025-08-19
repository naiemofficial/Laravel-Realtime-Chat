@php
    $auth_user = auth()->user();
@endphp
<div class="h-full">
    <livewire:chat.conversation-alert />

    <livewire:chat.add-conversation />

    @if($conversations->count() < 1)
        <livewire:chat.conversation-not-available />
    @else
        <ul role="list" id="conversations" class="divide-y divide-gray-100 space-y-1 mt-2">
            @foreach($conversations as $index => $conversation)
                <li
                    wire:key="{{ $conversation->id }}"
                    wire:click="openConversation({{ $conversation->id }})"
                    x-data="{ show: false }"
                    x-init="setTimeout(() => show = true, {{ $animation ? ($index * 80) : 0 }})"
                    x-show="show"
                    x-transition.duration.300ms
                    style="transition: 0.3s"
                    @class([
                        'flex justify-between gap-x-6 px-3 py-3 transition-[0.3s] bg-white rounded-sm cursor-pointer',
                        'ring-2 ring-blue-400' => $openedConversation === $conversation->id,
                    ])
                    aria-selected="{{ $openedConversation === $conversation->id ? 'true' : 'false' }}"
                >
                    <div class="flex min-w-0 gap-x-4 w-full">
                        <span class="inline-flex items-center justify-center h-[48px] w-[48px] min-w-[48px] border border-double border-gray-200 rounded-sm text-gray-300 bg-gray-100 text-xl">
                            <i class="fa-duotone fa-solid fa-user"></i>
                        </span>
                        @php
                            $me_Participant = $conversation->participant($auth_user);
                            $me = $me_Participant->user(); // Participant (me) as user
                            $participant = $conversation->participant($auth_user, exclude: true)->user; // Participant as User
                        @endphp
                        <div class="flex flex-col w-full">
                            <div class="min-w-0 flex flex-auto w-full flex-row gap-3 justify-between">
                                <p class="text-sm/6 font-semibold text-gray-900 truncate">{{ $participant->name }}</p>
                                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                    <div class="inline-flex items-center space-x-3 justify-center text-xs/5 text-gray-500">
                                        <span title="updated"><i class="fa-light fa-clock mr-1"></i> {{ $conversation->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-flex justify-between w-full items-end">
                                <span class="mt-1 leading-[1] truncate text-xs/5 text-gray-500">{{ $participant->uid }}</span>
                                @if(!$me_Participant->seen_conversation)
                                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                                @endif

{{--                                <span class="inline-flex items-center h-4 px-3 text-xs bg-green-100 text-green-500 rounded-full font-medium"><span class="inline-flex items-center justify-center gap-x-1" style="zoom: 0.9"><i class="fa-solid fa-phone"></i> Audio Call</span></span>--}}
{{--                                <span class="inline-flex items-center h-4 px-3 text-xs bg-purple-100 text-purple-500 rounded-full font-medium"><span class="inline-flex items-center justify-center gap-x-1" style="zoom: 0.9"><i class="fa-solid fa-video"></i> Video Call</span></span>--}}
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <x-pagination :data="$conversations" class="inner-col-pagination left-0 rounded-br-none rounded-bl-md"/>
</div>
