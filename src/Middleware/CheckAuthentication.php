<?php

namespace Ananta\UserManagement\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guard = \Auth::guard(config('permission.guard'));
        $user= $guard->user();
        if ($guard->guest() && !$this->shouldPassThrough($request)) {
            return redirect()->guest(route(config('permission.guest_redirect')));
        }

        if($user && $user->status === 0){ 
            $guard->logout();
            return redirect()->guest(route(config('permission.guest_redirect')))
                ->with([
                    'inactive' => 'Your account is inactive'
                ]);
        }

        if($guard->guest() && $this->shouldPassThrough($request)){
            return $next($request);
        }

         // Allow access route
        if ($this->routeDefaultPass($request)) {
             return $next($request);
        }
        // check permissions
        if($user->checkUrlAllowAccess($request->url())){
            return $next($request);
        }else{
            return $next($request); //remove this for permission
            abort(401);
        }
        return $next($request);
    }


    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $routePath = $request->path();
        $exceptsPath = config('permission.without');
        return in_array($routePath, $exceptsPath);
    }


      /*
    Check route defualt allow access
    */
    public function routeDefaultPass($request)
    {
        $routeName = $request->route()->getName();
        $allowRoute =config('permission.allow');
        return in_array($routeName, $allowRoute);
    }

}
