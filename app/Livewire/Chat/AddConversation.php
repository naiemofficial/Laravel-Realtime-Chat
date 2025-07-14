<?php

namespace App\Livewire\Chat;

use App\Helpers\Response;
use App\Http\Controllers\ConversationController;
use App\Http\Middleware\GuestAuth;
use App\Models\Conversation;
use Livewire\Component;

class AddConversation extends Component
{
    public $uid;

    public function submit()
    {
        $uid = $this->uid;
        request()->attributes->set('suggestion', true);
        $response = app(GuestAuth::class)->handle(request(), function($request) use ($uid){
            $request->merge(['uid' => $uid]);

            $ConversationController = new ConversationController();
            return $ConversationController->store($request);
        });

        $this->dispatch('refresh-conversation-alert', response: $response);

        if($response->isSuccessful()){
            $this->reset();
            $this->dispatch('refresh-conversations'); // Conversation  Created
            $createdConversationId = $response->getData()?->conversation_id;
            $this->dispatch('open-Conversation', id: $createdConversationId);
        }
    }


    public function render()
    {
        return view('livewire.chat.add-conversation');
    }
}
