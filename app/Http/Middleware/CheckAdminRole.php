<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::guard('admin')->check()) {
            return Redirect::route("admin.login");
        } else {
            $post = Admin::where('email', Auth::guard('admin')->user()->email)->value("post");

            if (!$post || !in_array($post, $roles)) {
                return Redirect::route("admin.login");
            }
        }
        return $next($request);

    }
}
