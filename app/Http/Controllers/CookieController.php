<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use App\Http\Requests\CookieRequest;
use App\Models\Cookie as DBCookie;
use Illuminate\Support\Facades\Cookie;
use Carbon\Carbon;
use function Laravel\Prompts\error;
use function PHPUnit\Framework\isEmpty;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Log;

class CookieController extends Controller
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
    private function calculateCookieLifespan($date){
        if(isEmpty($date)){
            $lifespan = 0;
        } else if(Carbon::parse($date)->isValid()){
            $lifespan = now()->diffInMinutes(Carbon::parse($date), false);
            if($lifespan <= 0){
                $lifespan = 0;
            }
        } else {
            $lifespan = 60;
        }
        return $lifespan;
    }
    public function store(CookieRequest $request)
    {
        try {
            $validated = $request->validated();
            $cookie = DBCookie::create($validated);

            // Create Cookie in the Browser
            Cookie::queue($validated['name'], $validated['value'], $this->calculateCookieLifespan($validated['expires_at'] ?? 0));
            return response()->json([
                'success' => 'Cookie added successfully',
                'cookie' => $cookie
            ], 201);

        } catch (\Exception $e){
            Log:error("Cookie Creation: " . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $name)
    {
        $cookie_name = $name;
        $cookie_value = 'test'; // Cookie::get($cookie_name);
        $db_cookie = DBCookie::where([
            ['name', '=', $cookie_name],
            ['value', '=', $cookie_value]
        ])->first();

        dd($db_cookie->guest()->name);
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
