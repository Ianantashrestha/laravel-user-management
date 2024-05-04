<?php

namespace IAnanta\UserManagement\Middleware;

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
        
        if($this->without($request)){
            return $next($request);
        }

        if ($guard->guest() && !$this->shouldPassThrough($request)) {
              if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'],401);
              }else{
                return redirect()->guest(route(config('permission.guest_redirect')));

              }
        }

        if($user && $user->status === 0){ 
            $guard->logout();
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Your account is inactive'],500);
            }else{
                return redirect()->guest(route(config('permission.guest_redirect')))
                ->with([
                    'inactive' => 'Your account is inactive'
                ]);
            }
         
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

    public function without($request){
        $route = \Route::getRoutes()->match($request);
        $routeName =  $route->getName();
        $allowRoute =config('permission.without');
        return in_array($routeName, $allowRoute);
    }



      /*
    Check route defualt allow access
    */
    public function routeDefaultPass($request)
    {
        $route = \Route::getRoutes()->match($request);
        $routeName =  $route->getName();
        $allowRoute =config('permission.allow');
        return in_array($routeName, $allowRoute);
    }

}
