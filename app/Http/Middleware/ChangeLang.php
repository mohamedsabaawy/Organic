<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ChangeLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('ar');
        if ($request->header('accept_language'))
            app()->setLocale($request->header('accept_language'));
        if (Auth::guard('api')->check()) {
           if ( app()->setLocale(Auth::guard('api')->user()->lang) <> $request->header('accept_language')){
              $client= Client::find(Auth::guard('api')->id());
              $client->lang=$request->header('accept_language');
              $client->save();
           }
            app()->setLocale($request->header('accept_language'));
        }
        return $next($request);
    }
}
