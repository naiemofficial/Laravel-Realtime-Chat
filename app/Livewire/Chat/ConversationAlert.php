<?php

namespace App\Livewire\Chat;

use App\Helpers\Response;
use App\Models\Conversation;
use Livewire\Attributes\On;
use Livewire\Component;

class ConversationAlert extends Component
{
    #[On('refresh-conversation-alert')]
    public function refreshConversationAlert($response): void {
        Response::visualize(Conversation::class, $response, [
            'session-flash' => true,
            'template' => [
                'key' => 'textOnly',
                'wrapper' => true,
                'key-based-color' => true,
                'class' => 'line-clamp-2 px-2 py-0.5 border border-solid rounded-[20px]'
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.chat.conversation-alert', [
            'className' => Conversation::class
        ]);
    }
}
