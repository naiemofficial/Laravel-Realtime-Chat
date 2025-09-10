<?php

namespace App\Livewire\Chat;

use App\Events\ConversationConnection;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\UserAuth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Call as CallModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use PhpParser\Node\Expr\Cast\Object_;
use ReflectionMethod;
use stdClass;


class Call extends Component
{
    public array $temp = [];

    public ?Conversation $Conversation = null;
    public ?Message $Message = null;
    public ?CallModel $Call = null;

    public array $call = [];

    public ?User $peerUser = null;

    public bool $sendingCall = false;
    public bool $incomingCall = false;
    public ?string $callText = null;

    public $settings = null;
    public $peerSettings = null;
    public function init_settings(): void {
        $this->settings = (object) [
            'mic'       => true,
            'camera'    => true,
            'ringing'   => false,
            'ringTime'  => 6000,
            'stream'    => null
        ];
        $this->peerSettings = $this->settings;
    }

    public function callArray() : array {
        $call               = $this->Call->toArray();
        $call['settings']   = $this->settings;
        $call['caller']     = $this->Call->caller()?->only(['id', 'name']);
        return $call;
    }


    private function callInProgress() : void {
        $this->dispatch('refresh-message-alert', response: ['error' => 'A call is already in progress.']);
    }
    private function incomingCallID(){
        $this->dispatch('refresh-message-alert', response: ['error' => 'You have a incoming call, but Call ID not found!']);
    }


    public function init(int $message_id): void {
        $this->reset();
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
            $this->call = $this->callArray() ?? [];
        }
    }
    private function init_temp(int $message_id): object {
        $temp = new \stdClass();
        $temp->Message = Message::find($message_id);

        $temp->Call = $temp->Message?->call();
        if (!$temp->Call) $this->incomingCallID();
        return $temp;
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
        if($this->sendingCall){
            $this->callInProgress();
            return;
        }

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
        $this->dispatch('request-for-media-permission', $this->Call->type);

        $this->peerUser = $this->Call?->receiver();
        $this->callText = 'Calling';
        $this->sendingCall = true;
        if(empty($this->settings)) $this->init_settings();

        $this->broadcastCall();
    }

    public function broadcastCall(bool $checkCondition = false, $data = []): void {
        if(!$checkCondition || ($this->Call?->status === 'pending' && $this->peerSettings?->ringing === false)){
            broadcast(new ConversationConnection($this->Conversation, Auth::user(), $this->Message, $data));
        }
    }




    #[On('incoming-call')]
    public function incomingCall(int $message_id, array $data = []): void {
        $data = (sizeof($data) > 0 ? (object) $data : null);
        $skipBusy = ($data?->skipBusy === true) ? true : false;

        if($this->Call?->exists() && !$skipBusy){
            $this->callBusy($message_id);
            return;
        }


        $this->init($message_id);
        $this->peerUser = $this->Call?->caller();
        $this->callText = 'Incoming call';

        if ($this->Call instanceof CallModel && $this->Call?->exists()) {
            $already_ended = in_array($this->Call->status, ['cancelled', 'declined', 'ended', 'stopped']);
            if(!$already_ended){
                $this->incomingCall = true;
                if(empty($this->settings)) $this->init_settings();

                // Send a response to caller that call is ringing
                $this->settings->ringing = true;
                $this->sendMySettingsToPeer();
            }
        }
    }

    private function callBusy($message_id){
        $this->temp[$message_id] = $this->init_temp($message_id);

        $this->temp[$message_id]?->Call?->update([ 'status' => 'busy' ]);
        $this->WS_send([ 'call' => $this->temp[$message_id]?->Call, 'type' => 'FUNCTION', 'function' => 'cancelDeclineEndCall', 'args' => ['by_self' => false]]);
        $this->temp[$message_id] = null;
    }



    public function cancelDeclineEndCall(bool $by_self = true): void {
        if($this->Call instanceof CallModel && $this->Call?->exists()){
            $already_ended = in_array($this->Call->status, ['cancelled', 'declined', 'ended', 'stopped']);

            if($this->sendingCall){
                if($by_self && !$already_ended ){
                    if($this->Call->status === 'accepted'){
                        $this->Call->update(['status' => 'ended', 'ended_at' => now()]);
                    } else {
                        $this->Call->update(['status' => 'cancelled']);
                    }
                }
                $this->sendingCall = false;
            } elseif($this->incomingCall){
                if($by_self && !$already_ended){
                    if($this->Call->status === 'accepted'){
                        $this->Call->update(['status' => 'ended', 'ended_at' => now()]);
                    } else {
                        $this->Call->update(['status' => 'declined']);
                    }
                }
                $this->incomingCall = false;
            }

            $this->Call->refresh();
            $this->Message->call = $this->callArray() ?? [];

            if($by_self){
                $this->WS_send([ 'type' => 'FUNCTION', 'function' => 'cancelDeclineEndCall', 'args' => ['by_self' => !$by_self] ]);
            }

            $this->dispatch('stop-tune');
            $this->dispatch('stop-stream', $this->settings?->stream);
            $this->dispatch('execute-drop-message', message: $this->Message);
            $this->reset();
        }
    }


    public function stopCall(): void {
        $already_ended = in_array($this->Call->status, ['cancelled', 'declined', 'ended', 'stopped']);
        if(!$already_ended){
            $this->Call->update(['status' => 'stopped', 'ended_at' => now()]);
            $this->Call->refresh();
            $this->sendingCall = false;
            $this->incomingCall = false;
            $this->Message->call = $this->callArray() ?? [];
            $this->WS_send([ 'type' => 'FUNCTION', 'function' => 'cancelDeclineEndCall', 'args' => ['by_self' => false] ]);
            $this->dispatch('execute-drop-message', message: $this->Message);
            $this->reset();
        }
    }




    public function receiveCall(){
        $this->Call?->update(['status' => 'accepted', 'accepted_at' => now(), 'last_ping' => now()]);
        $this->WS_send([ 'type' => 'FUNCTION', 'functions' => [
            'refreshCall',
            'updatePeerSettings' => ['settings' => $this->settings], // Sending my settings to peer
            'sendMySettingsToPeer' // Receive back peer settings
        ] ]);

    }

    private function refreshCall(): void {
        $this->Call?->refresh();
    }

    private function updatePeerSettings(array $settings): void {
        $this->dispatch('refresh-peer-settings', $settings);
        $this->peerSettings = (object) $settings;
    }

    private function sendMySettingsToPeer(): void {
        $this->WS_send([ 'type' => 'FUNCTION', 'function' => 'updatePeerSettings', 'args' => ['settings' => $this->settings] ]);
    }

    public function muteUnmute(): void {
        $this->settings->mic = !$this->settings->mic;
        if($this->Call?->status === 'accepted'){
            $this->sendMySettingsToPeer();
        }
        $this->dispatch('mute-unmute', $this->settings->mic);
    }

    public function cameraOnOff(): void {
        $this->settings->camera = !$this->settings->camera;
        if($this->Call?->status === 'accepted'){
            $this->sendMySettingsToPeer();
        }
        $this->dispatch('camera-on-off', $this->settings->camera);
    }









    #[On('WS_send')]
    public function WS_send($data){
        $custom_header = ['to' => 'CALL', 'call' => $this->call];
        $data = array_merge($custom_header, $data);
        broadcast(new ConversationConnection($this->Conversation, Auth::user(), NULL, $data));
    }

    #[On('WS_send_x')]
    public function WS_send_x($data){
        $custom_header = ['to' => 'CALL', 'call' => $this->call];
        $data = array_merge($custom_header, $data);
    }







    #[On('WS_Receive')]
    public function WS_Receive($response){
        $response = (object) $response;
        $fromCall = (object) ($response?->call ?? []);

        if(($fromCall?->id === $this->Call?->id) || (($response?->MANDATE ?? null) === true)){
            if($response?->type === 'RESPONSE'){
                $this->WS_Response($response);
            }


            else if($response?->type === 'ACTION'){
                $action = $response?->action;
            }


            else if ($response?->type === 'FUNCTION') {
                $this->call_function($response);
            }


        }
    }



    private function WS_Response($_RESPONSE): void {
        $response = $_RESPONSE?->response;
        /*if($response === 'ringing'){
            $this->callText = $_RESPONSE?->text ?? $this->callText;
        }*/
    }

















    /**
     * @throws \Exception
     */
    private function call_function($response){
        $functions = [];

        // Function
        if(!empty($response?->function)){
            $functions[] = [ 'name' => $response?->function, 'args' => $response?->args ?? [] ];
        }

        // Functions
        if (!empty($response?->functions)) {
            foreach ($response?->functions as $index => $function) {
                $fn_name = is_array($function) ? $index : $function;
                $fn_args = is_array($function) ? $function : [];

                $functions[] = [ 'name' => $fn_name, 'args' => $fn_args ];
            }
        }

        foreach($functions as $function){
            $function_name = $function['name'];
            if(method_exists($this, $function_name)){
                $function_args = $function['args'] ?? [];

                $method = new ReflectionMethod($this, $function_name);
                $ordered_args = [];

                foreach($method->getParameters() as $param){
                    $param_name = $param->getName();
                    if(array_key_exists($param_name, $function_args)){
                        $ordered_args[] = $function_args[$param_name];
                    } elseif($param->isDefaultValueAvailable()){
                        $ordered_args[] = $param->getDefaultValue();
                    } else {
                        throw new Exception("Missing required argument '{$param_name}' for function '{$function_name}'");
                    }
                }
                $method->invokeArgs($this, $ordered_args);
            } else {
                throw new Exception("Method '{$function_name}' does not exist in class " . __CLASS__);
            }
        }
    }














    public function refresh(): void {
        if(!$this->stop_refresh){
            if($this->Call instanceof CallModel && $this->Call?->exists()){
                $this->elapsed_call_time++;
                if($this->elapsed_call_time > $this->max_call_pickup_time){
                    $this->cancelDeclineEndCall();
                }
            }
        }
    }

    public function pingCall(){
        $this->Call?->update(['last_ping' => now()]);
    }




    // Stream
    public function setStream($stream){
        $this->settings->stream = $stream;
    }



    // Render
    public function updateTemp(string $key, $value){
        $this->temp[$key] = $value;
    }


    public function render(){
        return view('livewire.chat.call');
    }
}
