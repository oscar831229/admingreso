<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Log;

use App\Models\His\LogApiRequest;

class LogRequests
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
        $request->start = microtime(true);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $request->end = microtime(true);

        $this->log($request,$response);
    }

    protected function log($request,$response)
    {
        $duration = $request->end - $request->start;
        $url = $request->fullUrl();
        $method = $request->getMethod();
        $ip = $request->getClientIp();
        $user_id = $request->user()->id ?? 0;
        $response = $response->getContent();

        # Registra log base de datos
        LogApiRequest::create([
            'user_id' => $user_id, 
            'method' => $method, 
            'url' =>$url, 
            'response' => $response, 
            'ip' => $ip
        ]);

        # Registrar logs API
        // $log = "{$ip}: {$method}@{$url} - {$duration}ms \n".
        // "Request : {[$request->all()]} \n".
        // "Response : {$response->getContent()} \n";

        // Log::info($log);
    
    }
    
}
