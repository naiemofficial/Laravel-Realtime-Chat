<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CookieController;
use App\Http\Controllers\GuestController;
use App\Http\Requests\CookieRequest;
use App\Http\Requests\GuestRequest;
use App\Models\Guest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Cast\Object_;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use App\Models\Cookie as DBCookie;
use Illuminate\Support\Facades\Http;
use function PHPUnit\Framework\isEmpty;

class GuestCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate new Guest if current Guest isn't registered or not valid
        if(!Guest::isValid()){
            $request_init = $request->all();
            $has_guest_name = $request->has('name');

            // Cookie
            $Cookie = new DBCookie();
            {
                // Prepare CookieRequest
                $request->merge([
                    'name' => Guest::$cookie_name,
                    'value' => Str::uuid()->toString()
                ]);
                $CookieRequest = app(CookieRequest::class)->merge($request->all());

                // New CookieController Instance
                $CookieController = new CookieController();
                $response = $CookieController->store($CookieRequest);

                if($response->isSuccessful()){
                    $response_data = $response->getData();
                    $cookie = $response_data->cookie;
                    $Cookie = DBCookie::find($cookie->id);
                }
            }

            // Guest
            $request->replace($request_init);
            if($has_guest_name){
                $request->merge(['cookie_id' => $Cookie->id]);
                $GuestRequest = app(GuestRequest::class)->merge($request->all());

                $GuestController = new GuestController();
                $response = $GuestController->store($GuestRequest);

                if($response->isSuccessful()){
                    return response()->json([
                        'type' => 'success',
                        'cookie' => $Cookie->id,
                        'message' => 'Successfully created guest!',
                    ], 201);
                } else {
                    $Cookie->delete(); // Delete the created cookie since Guest creation is failed!
                    return $response;
                }
            }
        }

        return $next($request);
    }
}
