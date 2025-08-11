<?php

namespace App\Livewire\Chat;

use App\Http\Controllers\ConversationController;
use App\Http\Middleware\UserAuth;
use App\Models\Conversation;
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


    #[On('view-conversation')]
    public function viewConversation(int $id){
        $this->Conversation = Conversation::find($id);
        $Conversation  = $this->Conversation;
        $this->conversationId = $id;


        request()->attributes->set('suggestion', true);
        $response = app(UserAuth::class)->handle(request(), function($request) use($Conversation){
            $ConversationController = app(ConversationController::class);
            return $ConversationController->show($Conversation);
        });

        if($response->isSuccessful()){
            $this->participant = $Conversation->participant(auth()->user());
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
        return User::find($id);
    }

    public function render()
    {
        return view('livewire.chat.messages');
    }
}
