<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestRequest;
use App\Models\Guest;
use Illuminate\Http\Request;
use App\Models\Cookie as DBCookie;
use Illuminate\Validation\Rule;

class GuestController extends Controller
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
    public function store(GuestRequest $request)
    {
        try {
            $validated = $request->validated();

            // Generate the UID
            $uid = 'G' . str_pad(Guest::max('id') + 1, 5, '0', STR_PAD_LEFT);
            $validated = array_merge($validated, ['uid' => $uid]);

            $Guest = Guest::create($validated);

            return response()->json([
                'key' => 'success',
                'message' => 'Successfully created guest!',
                'guest' => [
                    'id' => $Guest->id,
                    'name' => $Guest->name,
                ]
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
    public function show(Guest $guest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $guest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'name' => ['string', 'required', 'max:255'],
        ]);
        Guest::where('id', $guest->id)->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        //
    }
}
