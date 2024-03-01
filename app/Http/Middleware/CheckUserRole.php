<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
//        dd($request->user()->role->name);
        // Check if the authenticated user has the role of "admin"
        if ($request->user() && $request->user()->role->name !== 'admin') {

            // Redirect to the home page with a flash message
            session()->flash('error', trans('Access denied'));
            return redirect('/home');
        }

        // Continue to the next middleware or the controller
        return $next($request);
    }
}
