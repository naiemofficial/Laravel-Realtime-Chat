<?php

namespace App\Livewire\Chat;

use App\Http\Controllers\MessageController;
use App\Http\Middleware\UserAuth;
use Livewire\Component;

class SendMessage extends Component
{
    public $message;
    public $conversationId;

    public function send(){
        $conversationId = $this->conversationId;
        $message = $this->message;
        $response = app(UserAuth::class)->handle(request(), function($request) use ($conversationId, $message){
            $request->merge([
                'conversation_id'   => $conversationId,
                'message'           => $message
            ]);

            $MessageController = app(MessageController::class);
            return $MessageController->store($request);
        });

        if($response->isSuccessful()){
            $Message = $response->getData()->message;
            $this->reset('message');

            if($Message->type !== 'call'){
                $this->dispatch('execute-drop-message', message: $Message);
                $this->dispatch('refresh-conversations', data: ['animation' => false]);
            }
        } else {
            $this->dispatch('refresh-message-alert', response: $response);
        }
    }
    public function render()
    {
        return view('livewire.chat.send-message');
    }
}
