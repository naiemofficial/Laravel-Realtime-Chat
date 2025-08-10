<x-layout>
    <x-slot:headerRight>
        Filter
    </x-slot:headerRight>

    <div class="h-full bg-white border border-solid rounded-md border-gray-200 mx-auto flex">
        <div class="p-8 w-1/3 border-r-1 border-r-solid border-r-inherit rounded-tl-md rounded-bl-md flex flex-col bg-gray-100 relative">
            <livewire:chat.conversations />
        </div>
        <div class="w-2/3 h-full flex flex-col relative">
            <livewire:chat.messages />
        </div>
    </div>
</x-layout>

