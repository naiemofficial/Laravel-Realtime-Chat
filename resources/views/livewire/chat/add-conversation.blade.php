<div class="flex items-center border rounded-lg px-3 py-1 pr-[6px] bg-white border-gray-300 duration-300 ease-in-out focus-within:shadow-[0_0_0_2px_#2563eb]">
    <span class="min-w-[20px] text-gray-500 mr-2 inline-flex items-center justify-center">
        <i wire:loading.remove wire:target="submit" class="fa-duotone fa-solid fa-messages"></i>
        <i wire:loading wire:target="submit" class="fas fa-circle-notch fa-spin"></i>
    </span>

    <input
        wire:model="uid"
        name="uid"
        wire:loading.attr="disabled"
        wire:target="submit"
        type="text" class="min-w-0 flex-auto bg-transparent text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0 py-1 text-sm disabled:text-gray-400 border-0"
        placeholder="Enter User UID"
    >
    <button
        type="submit"
        wire:click="submit"
        wire:loading.attr="disabled"
        class="min-w-[80px] ml-2 bg-gray-800 text-white px-4 py-1 rounded-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-600 text-sm cursor-pointer disabled:cursor-not-allowed"
    >
        Submit
    </button>
</div>
