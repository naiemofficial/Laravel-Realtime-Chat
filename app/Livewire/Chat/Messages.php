<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class Messages extends Component
{
    public $messages;
    public function render()
    {
        return view('livewire.chat.messages');
    }
}
