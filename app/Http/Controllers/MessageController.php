<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MessageController extends Controller
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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id'   => ['required', Rule::exists('conversations', 'id')],
                'message'           => ['required', 'string'],
            ]);

            $Conversation = Conversation::find($validated['conversation_id']);
            $Sender         = Auth::user();
            $message        = $validated['message'];


            $Message = Message::create([
                'conversation_id'   => $Conversation->id,
                'user_id'           => $Sender->id,
                'text'              => $message,
                'type'              => 'regular'
            ]);



            // Broadcast the message
            broadcast(new MessageSent($Conversation, $Sender, $message));


            return response()->json([
                'success' => 'Message sent successfully',
                'message' => $Message
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
