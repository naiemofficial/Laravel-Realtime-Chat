<?php

namespace App\Livewire\Chat;

use App\Http\Controllers\MessageController;
use App\Http\Middleware\UserAuth;
use Illuminate\Support\Facades\Broadcast;
use Livewire\Component;
use App\Livewire\Chat\Call;

class SendCall extends Component
{
    public $conversationId;
    public $participantId;

    public function voiceCall(){
        $this->dispatch('start-voice-call', conversation_id: $this->conversationId);
    }

    public function videoCall(){
        $this->dispatch('start-video-call', conversation_id: $this->conversationId);
    }


    public function render()
    {
        return view('livewire.chat.send-call');
    }
}
