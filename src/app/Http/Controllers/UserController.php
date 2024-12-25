<?php

namespace App\Http\Controllers;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
	protected $userService;

	public function __construct(UserService $userService)
	{
		$this->userService = $userService;
	}

	/**
	 * Get a list of users with their roles.
	 */
	public function index()
	{
		$users = $this->userService->getAllUsers();
		return response()->json($users, 200);
	}

	/**
	 * Store a new user and assign a role.
	 */
	public function store(Request $request)
	{
		// Use the UserService to create a user
		$result = $this->userService->createUser($request->all());

		if ($result['status']) {
			return response()->json(['message' => $result['message'], 'user' => $result['user']], 201);
		}

		return response()->json(['error' => $result['message']], 500);
	}

	/**
	 * Update a user.
	 */
	public function update(Request $request, $id)
	{
		// Use the UserService to update a user
		$result = $this->userService->updateUser($request->all());

		if ($result['status']) {
			return response()->json(['message' => $result['message'], 'user' => $result['user']], 201);
		}
		
		return response()->json(['error' => $result['message']], 500);
	}

	/**
	 * Delete a user.
	 */
	public function destroy($id)
	{
		$result = $this->userService->deleteUser($id);

		if ($result['status']) {
			return response()->json(['message' => $result['message']], 200);
		}

		return response()->json(['error' => $result['message']], 500);
	}
}