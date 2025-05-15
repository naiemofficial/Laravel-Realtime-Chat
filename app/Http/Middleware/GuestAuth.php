<?php

namespace App\Http\Middleware;

use App\Models\Guest;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Guest::isValid()){
            $response = ['error' => 'You are not authenticated'];

            $suggestion = $request->attributes->get('suggestion', false);
            if($suggestion){
                $response['warning'] = 'Please submit your name first';
            }

            return response()->json($response, 401);
        }
        return $next($request);
    }
}
