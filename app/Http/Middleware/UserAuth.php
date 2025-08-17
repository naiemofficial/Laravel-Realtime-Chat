<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): bool|Response
    {
        if(!Auth::check()){
            $response = ['error' => 'You are not authenticated'];

            $suggestion = $request->attributes->get('suggestion', false);
            if($suggestion){
                $response['warning'] = 'Please complete the registration first!';
            }

            return response()->json($response, 401);
        }
        return $next($request);
    }
}
