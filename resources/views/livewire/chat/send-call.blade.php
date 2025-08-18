<div class="inline-flex items-center gap-x-3">
    <!-- Audio Call Button -->
    <button type="button"
            wire:click="voiceCall"
            class="px-3 py-1 rounded-full bg-green-500 text-white hover:bg-green-600 active:scale-95 transition disabled:bg-gray-400">
        <i class="fa-solid fa-phone"></i>
    </button>

    <!-- Video Call Button -->
    <button type="button"
            wire:click="videoCall"
            class="px-3 py-1 rounded-full bg-purple-500 text-white hover:bg-purple-600 active:scale-95 transition disabled:bg-gray-400">
        <i class="fa-solid fa-video"></i>
    </button>
</div>
