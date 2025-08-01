<?php

namespace App\Livewire\Chat;

use App\Http\Controllers\ConversationController;
use App\Http\Middleware\GuestAuth;
use App\Models\Conversation;
use App\Models\Guest;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class Messages extends Component
{
    public $messages = [];
    public $recipient = null;
    public $Conversation = null;
    public $currentGuest = null;
    public $conversationSelected = false;
    public $conversationId;


    #[On('view-conversation')]
    public function viewConversation($id){
        $this->Conversation = Conversation::find($id);
        $Conversation  = $this->Conversation;
        $this->conversationId = $id;


        request()->attributes->set('suggestion', true);
        $response = app(GuestAuth::class)->handle(request(), function($request) use($Conversation){
            $ConversationController = app(ConversationController::class);
            return $ConversationController->show($Conversation);
        });

        if($response->isSuccessful()){
            $this->recipient = $Conversation->recipient();
            $this->messages = $response->getData()->messages;
            $this->conversationSelected = true;
        }
    }


    #[On('refresh-messages')]
    public function refreshMessages($conversationId){
        $ConversationController = app(ConversationController::class);
        $Conversation = Conversation::find($conversationId);
        $response = $ConversationController->show($Conversation);
        $this->messages = $response->getData()->messages;
    }


    public function sender($id){
        return Guest::find($id);
    }

    public function render()
    {
        $this->currentGuest = Guest::current();
        return view('livewire.chat.messages');
    }
}
