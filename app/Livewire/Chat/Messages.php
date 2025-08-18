<?php

namespace App\Livewire\Chat;

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ParticipantController;
use App\Http\Middleware\UserAuth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Messages extends Component
{
    public $messages = [];
    public $participant = null;
    public $Conversation = null;
    public $conversationSelected = false;
    public $conversationId;
    public $MessageInstance;

    public function mount() {
        $this->MessageInstance = app(Message::class);
    }


    #[On('view-conversation')]
    public function viewConversation(int $id){
        $this->Conversation = Conversation::find($id);
        $Conversation  = $this->Conversation;
        $this->conversationId = $id;

        $ConversationController = app(ConversationController::class);
        $this->messages = $ConversationController->show($Conversation);

        $this->participant = $Conversation->participant(auth()->user(), exclude: true)->user();
        $this->conversationSelected = true;
        $this->dispatch('seen-conversation-incoming-message', openedConversation: $Conversation->id);
        $this->dispatch('refresh-conversation');

    }


    #[On('refresh-messages')]
    public function refreshMessages($conversationId){
        $ConversationController = app(ConversationController::class);
        $Conversation = Conversation::find($conversationId);
        $this->messages = $ConversationController->show($Conversation);
    }


    public function sender($id){
        return User::find($id);
    }

    public function render()
    {
        return view('livewire.chat.messages');
    }
}
