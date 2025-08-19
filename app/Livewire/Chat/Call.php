<?php

namespace App\Livewire\Chat;

use App\Events\ConversationConnection;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\UserAuth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Call as CallModel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use ReflectionMethod;

class Call extends Component
{
    public ?Conversation $Conversation = null;
    public ?Message $Message = null;
    public ?CallModel $Call = null;
    public array $call = [];
    public array $fromCall = [];
    public $Participant;

    public ?int $recipientId = null;
    public bool $sendingCall = false;
    public bool $incomingCall = false;
    public ?string $callText = null;
    public $calling = false;
    public ?string $callStatus = null;
    public bool $isMuted = false;

    private function callInProgress() : void {
        $this->dispatch('refresh-message-alert', response: ['error' => 'A call is already in progress.']);
    }
    private function incomingCallID(){
        $this->dispatch('refresh-message-alert', response: ['error' => 'You have a incoming call, but Call ID not found!']);
    }


    public function init(int $message_id): void {
        $this->Message  = Message::find($message_id);
        $this->Conversation = Conversation::find($this->Message?->conversation_id);


        if($this->Conversation?->exists() && $this->Message?->exists()){
            if($this->Message->user_id === Auth::user()->id){ // Call started by the current user
                $this->Call = CallModel::create([
                    'message_id'    => $this->Message->id,
                    'type'          => $this->Message->text,
                    'status'        => 'pending',
                ]);
            } else {
                $this->Call = $this->Message->call();
                if(!$this->Call) $this->incomingCallID();
            }
            $this->call = $this->Call->toArray() ?? [];
        }


        $this->Participant = $this->Conversation?->participant(Auth::user(), exclude: true)?->user();
    }


    #[On('start-voice-call')]
    public function ___startVoiceCall($conversation_id){
        $this->startCall($conversation_id, 'voice');
    }


    #[On('start-video-call')]
    public function ___startVideoCall($conversation_id){
        $this->startCall($conversation_id, 'video');
    }

    private function startCall(int $conversation_id, string $call): void {
        $response = app(UserAuth::class)->handle(request(), function($request) use ($conversation_id, $call){
            $request->merge([
                'conversation_id'   => $conversation_id,
                'message'           => $call,
                'type'              => 'call',
            ]);
            $MessageController = app(MessageController::class);
            return $MessageController->store($request);
        });

        if($response->isSuccessful()){
            $response_data = $response->getData();
            $Message = $response_data->message;



            // Initialize the call
            $this->init($Message->id);

            $this->sendingCall();
        } else {
            $this->dispatch('refresh-message-alert', response: $response);
        }
    }


    private function sendingCall(): void {
        if($this->sendingCall){
            $this->callInProgress();
            return;
        }

        $this->callText = 'Calling...';
        $this->sendingCall = true;

        broadcast(new ConversationConnection($this->Conversation, Auth::user(), $this->Message));
    }



    #[On('incoming-call')]
    public function incomingCall(int $message_id): void {
        if($this->incomingCall){
            $this->callInProgress();
            return;
        }

        $this->init($message_id);

        $this->callText = 'Incoming call...';

        if ($this->Call instanceof CallModel && $this->Call?->exists()) {
            $this->incomingCall = true;

            // Send a response to caller that call is ringing
            $this->WS_send([ 'type' => 'RESPONSE', 'response'  => 'ringing', 'text' => 'Ringing...' ]);
        }
    }



    public function cancelDeclineCall(bool $by_self = true): void {
        if($this->Call instanceof CallModel && $this->Call->exists()){
            if ($this->sendingCall) {
                if($by_self){
                    $this->Call->update(['status' => 'cancelled']);
                    $this->WS_send([ 'type' => 'FUNCTION', 'function' => 'cancelDeclineCall', 'args' => ['by_self' => !$by_self] ]);
                }
                $this->sendingCall = false;
            } elseif ($this->incomingCall) {
                if($by_self) {
                    $this->Call->update(['status' => 'declined']);
                    $this->WS_send([ 'type' => 'FUNCTION', 'function' => 'cancelDeclineCall', 'args' => ['by_self' => !$by_self] ]);
                }
                $this->incomingCall = false;
            }

            $this->Message->call = $this->Call?->refresh()->toArray();
            $this->dispatch('execute-drop-message', message: $this->Message);
            $this->reset();
        }
    }

    public function receiveCall(){
        $this->Call->update(['status' => 'received']);
        // $this->WS_send([
        //     'to' => 'CALL', 'type' => 'ACTION', 'action' => 'declined', 'call' => $this->Call
        // ]);
    }












    private function WS_send($data){
        $custom_header = ['to' => 'CALL', 'call' => $this->Call];
        $data = array_merge($data, $custom_header);
        broadcast(new ConversationConnection($this->Conversation, Auth::user(), NULL, $data));
    }







    #[On('WS_Receive')]
    public function WS_Receive($response){
        $this->fromCall = $response['call'] ?? [];

        if(isset($this->fromCall['id']) && $this->fromCall['id'] === $this->Call->id){
            if($response['type'] === 'RESPONSE'){
                $this->WS_Response($response);
            }


            else if($response['type'] === 'ACTION'){
                $action = $response['action'];
                if($action === 'cancelled' || $action === 'declined'){
                    $this->cancelDeclineCall(by_self: false); // The action done by opponent
                }
            }


            else if ($response['type'] === 'FUNCTION') {
                $this->call_function($response);
            }


        }
    }



    private function WS_Response($_response): void {
        $response = $_response['response'];
        if($response === 'ringing'){
            $this->callText = !empty($_response['text']) ? $_response['text']: $this->callText;
        }
    }













    /**
     * @throws \Exception
     */
    private function call_function($response){
        $function = $response['function'];
        if (method_exists($this, $function)) {
            $method = new ReflectionMethod($this, $function);
            $input_args = $response['args'] ?? [];
            $ordered_args = [];

            foreach ($method->getParameters() as $param) {
                $name = $param->getName();
                if (array_key_exists($name, $input_args)) {
                    $ordered_args[] = $input_args[$name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $ordered_args[] = $param->getDefaultValue();
                } else {
                    throw new Exception("Missing required argument '{$name}' for function '{$function}'");
                }
            }
            $method->invokeArgs($this, $ordered_args);
        } else {
            throw new Exception("Method '{$function}' does not exist in class " . __CLASS__);
        }
    }



    public function refresh(): void {
        if($this->Call instanceof CallModel){

            if($this->incomingCall){

            }

        }
    }


    public function render(){
        return view('livewire.chat.call');
    }
}
