<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\{Logging};
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
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
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $response = $next($request);
            
            $log = [
                'URI' => $request->getUri(),
                'METHOD' => $request->getMethod(),
                'REQUEST_BODY' => $request->all(),
                'RESPONSE' => $response->getContent()
            ];
            Log::info(json_encode($log));

            $save_log = [
                'url' => $request->getUri(),
                'method' => $request->getMethod(),
                'request_body' => json_encode($request->all()),
                'response' => $response->getContent(),
                'user_agent' => $request->header('user-agent')
            ];
            Logging::create($save_log);

        } catch (Exception $e) {
            // dd($e);
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $code = 400;
                $result['status'] = false;
                $result['message'] = 'Token is Invalid';
                
                return response()->json($result,$code);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                $code = 400;
                $result['status'] = false;
                $result['message'] = 'Token is Expired';

                return response()->json($result,$code);
            }else{
                
                $code = 400;
                $result['status'] = false;
                $result['message'] = 'Authorization Token not found';

                return response()->json($result,$code);
            }
        }
        return $next($request);
    }
}