<?php

namespace App\Livewire\Chat;

use App\Events\ConversationConnection;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\UserAuth;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;

class Call extends Component
{
    public $conversationId;
    public $recipientId;

    public function audioCall(){
        $conversationId = $this->conversationId;
        $response = app(UserAuth::class)->handle(request(), function($request) use ($conversationId){
            $request->merge([
                'conversation_id'   => $conversationId,
                'message'           => 'audio',
                'type'              => 'call',
            ]);

            $ConversationController = app(MessageController::class);
            return $ConversationController->store($request);
        });


        if($response->isSuccessful()){
            $Message = $response->getData()->message;
            $this->dispatch('execute-drop-message', message: $Message);
            $this->dispatch('refresh-conversations', data: ['animation' => false]);
        } else {
            $this->dispatch('refresh-message-alert', response: $response);
        }

    }

    public function videoCall(){

    }

    public function render()
    {
        return view('livewire.chat.call');
    }
}
