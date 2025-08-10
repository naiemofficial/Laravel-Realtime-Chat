<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'uid' => ['required', 'regex:/^U[0-9]+$/', function ($attribute, $value, $fail) {
                    $userId = (int) substr($value, 1, -3);
                    if (!User::where('id', $userId)->exists()) {
                        $fail('Invalid UID');
                    }
                }]
            ]);

            $uid = $request->uid;


            // User itself
            $User = Auth::user();
            if($uid == $User->uid){
                return response()->json([
                    'warning' => 'You can\'t send messages to yourself.'
                ], 409);
            }


            // Recipient exists
            $Recipient = User::firstWhere('uid', $uid);
            if(!$Recipient){
                return response()->json([
                    'error' => 'Recipient could not be found.'
                ], 404);
            }


            // Check if conversation exists or not
            if($Conversation = Conversation::existsWith($User, $Recipient)){
                return response()->json([
                    'info' => 'A Conversation already exists with ' . $Recipient->name . "($Recipient->uid)",
                    'conversation_id' => $Conversation->id
                ], 200);
            }



            DB::beginTransaction();
            // Create Conversation
            $Conversation   = Conversation::create();
            $conversationId = $Conversation->id;

            Participant::create(['conversation_id' => $conversationId, 'user_id' => $User->id]);
            Participant::create(['conversation_id' => $conversationId, 'user_id' => $Recipient->id]);

            $Message = Message::create([
                'conversation_id'   => $conversationId,
                'user_id'           => $User->id,
                'text'              => 'started conversation',
                'type'              => 'starter'
            ]);
            DB::commit();


            return response()->json([
                'success' => 'Conversation created!',
                'conversation_id' => $conversationId
            ], 201);

        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        $messages = $conversation->messages();
        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }
}
