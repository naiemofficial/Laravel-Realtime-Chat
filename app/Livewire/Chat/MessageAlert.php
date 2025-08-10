<?php

namespace App\Livewire\Chat;

use App\Helpers\Response;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageAlert extends Component
{
    #[On('refresh-message-alert')]
    public function refreshMessageAlert($response): void {
        Response::visualize(Message::class, $response, [
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
        return view('livewire.chat.message-alert', [
            'className' => Message::class
        ]);
    }
}
