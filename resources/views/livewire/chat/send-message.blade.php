<div class="border-t-[1px] border-solid border-gray-200 w-full bg-gray-200 px-8 py-[6px] rounded-br-md">
    <div class="flex items-center bg-white border border-gray-300 rounded-full px-4 py-1 h-[36px] pr-1.5">
        <!-- Input Field -->
        <input
            type="text"
            wire:model.live="message"
            wire:loading.attr="disabled"
            wire:target="submit"
            placeholder="Type your message..."
            class="flex-grow bg-transparent outline-none text-gray-700 placeholder-gray-400] text-sm"
        >
        <!-- Send Button -->
        <button
            wire:click="send"
            wire:loading.attr="disabled"
            {{ strlen($message) < 1 ? "disabled" : "" }}
            class="bg-blue-500 hover:bg-blue-600 text-white rounded-full ml-2 h-[28px] w-[28px] flex items-center justify-center p-0 transition-all cursor-pointer disabled:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <i wire:loading.remove wire:target="submit" class="fa-solid fa-paper-plane text-[16px] pr-0.5 text-xs pointer-events-none"></i>
            <i wire:loading wire:target="submit" class="fas fa-circle-notch fa-spin pointer-events-none"></i>
        </button>
    </div>
</div>
