<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {


        $user = Group::query()->find(auth()->user()->group_id);
           if ($user->name=='admin'){
               return $next($request);
           }
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);
        foreach ($permissions as $permission) {
            if ($user->name == $permission) {
                return $next($request);
            }
        }
        $arr='USER DOES NOT HAVE THE RIGHT PERMISSIONS.';
         return response()->json($arr, 201);

    }
}
