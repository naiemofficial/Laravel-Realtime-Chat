<?php

namespace App\Livewire\Chat;

use App\Events\ConversationConnection;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\UserAuth;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Call extends Component
{
    public $conversationId = 0;
    public $Conversation;
    public $recipientId;
    public $sendingCall = false;
    public $incomingCall = false;
    public $Message;
    public $callText;


    public function mount(){
        $this->Message = new Message();
    }

    #[On('init-call')]
    public function init(array $data){
        $this->conversationId = $data['conversation_id'];
        $this->Conversation = Conversation::find($this->conversationId);
        $this->Message->conversation_id = $this->Conversation->id;
        $this->Message->participant_id = $this->Conversation->participant(Auth::user())->id;
    }

    public function checkCallStatus($conversationId){
        // if(empty($this->$conversationId)){
        //     $this->init(['conversation_id' => $conversationId]);
        // }
        $this->dispatch('log-test');
    }

    #[On('sending-call')]
    public function sendingCall(){
        // $this->init($data);

        $this->callText = 'Calling...';
        $this->sendingCall = true;

        /*$Conversation = $this->Conversation;
        $Message = $this->Message;z
        $Message->type = 'LISTEN';
        $Message->text = 'isCalling';*/

        /*while($this->sendingCall){
            app(UserAuth::class)->handle(request(), function() use($Conversation, $Message){
                broadcast(new ConversationConnection($Conversation, Auth::user(), $Message));
                return true;
            });

            sleep(1);
        }*/
    }


    #[On('incoming-call')]
    public function incomingCall(array $data){
        // $this->init($data);

        $this->callText = 'Incoming call...';
        $this->incomingCall = true;

        $Conversation = $this->Conversation;
        $Message = $this->Message;
        $Message->type = 'RESPONSE';
        $Message->text = 'calling';


        /*while($this->incomingCall){
            app(UserAuth::class)->handle(request(), function() use($Conversation, $Message){
                broadcast(new ConversationConnection($Conversation, Auth::user(), $Message));
                return true;
            });

            sleep(1);
        }*/
    }


    public function cancelDeclineCall(){
        $this->sendingCall = false;
        $this->incomingCall = false;
    }

    public function render()
    {
        return view('livewire.chat.call');
    }
}
