<div class="h-full flex justify-center items-center">
    <div class="text-center">
        @if($conversationSelected)
            <p class="text-lg text-gray-600"><i class="fa-duotone fa-thin fa-message-slash"></i></p>
            <p class="text-sm text-gray-400">There's no messages</p>
        @else
            <p class="text-lg text-gray-600">Conversation not selected</p>
            <p class="text-sm text-gray-400">Please select a conversation to see messages.</p>
        @endif
    </div>
</div>
