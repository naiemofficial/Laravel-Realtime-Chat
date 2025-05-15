<div class="min-w-[300px] flex items-center border rounded-lg px-3 py-1 pr-[6px] bg-white border-gray-300 duration-300 ease-in-out focus-within:shadow-[0_0_0_2px_#2563eb]">
    <span class="min-w-[20px] text-gray-500 mr-2 inline-flex items-center justify-center">
        <i wire:loading.remove wire:target="submit" class="fa-duotone fa-solid fa-messages"></i>
        <i wire:loading wire:target="submit" class="fas fa-circle-notch fa-spin"></i>
    </span>

    <input
        wire:model="uid"
        wire:loading.attr="disabled"
        wire:target="submit"
        type="text" class="flex-1 bg-transparent text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0 py-1 text-sm disabled:text-gray-400"
        placeholder="Enter User/Guest (U)ID"
    >
    <button
        type="submit"
        wire:click="submit"
        wire:loading.attr="disabled"
        class="min-w-[80px] ml-2 bg-blue-600 text-white px-4 py-1 rounded-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm cursor-pointer disabled:cursor-not-allowed"
    >
        Submit
    </button>
</div>
