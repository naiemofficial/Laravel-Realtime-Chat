<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Guest;
use App\Models\Message;
use App\Models\Participant;
use Illuminate\Http\Request;

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
                'uid' => ['required', 'regex:/^G[0-9]+$/']
            ]);

            $uid = $request->uid;


            // User/Guest itself
            $CurrentGuest = Guest::current();
            if($uid == $CurrentGuest->uid){
                return response()->json([
                    'warning' => 'You can\'t send messages to yourself.'
                ], 409);
            }


            // Recipient exists
            $Recipient = Guest::firstWhere('uid', $uid);
            if(!$Recipient){
                return response()->json([
                    'error' => 'Recipient could not be found.'
                ], 404);
            }


            // Check if conversation exists or not
            if(Conversation::existsWith($CurrentGuest, $Recipient)){
                return response()->json([
                    'info' => 'A Conversation already exists with ' . $Recipient->name . "($Recipient->uid)"
                ], 200);
            }



            // Create Conversation
            $Conversation   = Conversation::create();
            $Conv_ID = $Conversation->id;

            Participant::create(['conversation_id' => $Conv_ID, 'guest_id' => $CurrentGuest->id]);
            Participant::create(['conversation_id' => $Conv_ID, 'guest_id' => $Recipient->id]);

            $Message = Message::create([
                'conversation_id'   => $Conv_ID,
                'sender_id'         => $CurrentGuest->id,
                'text'              => 'started conversation',
                'type'              => 'starter'
            ]);


            return response()->json([
                'success' => 'Conversation created!',
                'conversation_id' => $Conv_ID
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
        //
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
