<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserRole;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request, Closure $next): Response
	{
		// Get the authenticated user
		$user = Auth::user();

		if (!$user) {
			return response()->json(['error' => User::getMessage('common', 'unauthorized')], 401);
		}

		// Get the current route's URI
		$currentRoute = $request->route()->uri();

		// Check if the user has the required role or permission
		if ($this->userHasPermission($user, $currentRoute)) {
			return $next($request);
		}

		// If not authorized, return a forbidden response
		return response()->json(['error' => User::getMessage('common', 'unauthorized')], 403);
	}

	/**
	 * Determine if the user has the required permissions for the given route.
	 *
	 * @param \App\Models\User $user
	 * @param string $routePath
	 * @return bool
	 */
	protected function userHasPermission(User $user, string $routePath): bool
	{
		// Fetch user roles
		$roles = $user->roles->pluck('id')->toArray();

		// Get all permissions associated with these roles
		$permissions = UserRole::whereIn('id', $roles)
			->with('permissions')
			->get()
			->pluck('permissions')
			->flatten()
			->unique('id');

		// Check if any permission matches the requested route
		return $permissions->contains(function ($permission) use ($routePath) {
			return trim($permission->api_route, '/') === trim($routePath, '/');
		});
	}
}