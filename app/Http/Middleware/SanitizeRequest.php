<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeRequest
{
    /**
     * Handle an incoming request.
     * I like to ensure I sanitize all incoming data. This is just a basic use of the middleware to sanitize all incoming data.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();

        // go through all the input and sanitize all data
        $input = filter_var_array($input, FILTER_SANITIZE_STRING);

        $request->replace($input);

        return $next($request);
    }
}
