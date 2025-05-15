<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class MessagesNotAvailable extends Component
{
    public $conversationSelected = false;
    public function render()
    {
        return view('livewire.chat.messages-not-available');
    }
}
