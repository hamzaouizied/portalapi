<?php

namespace App\Http\Middleware;

use App\Token;
use Closure;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uuid   = $request->header( 'uuid' );
        $token  = $request->bearerToken();
        $data = Token::where('token', '=', $token)->where('uuid', '=', $uuid)->first();
        if($data)
        {
            return $next($request);
        }
        return response()->json( [ 'error' => 'Unauthorized' ], 403 );
    }
}
