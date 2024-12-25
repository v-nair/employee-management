<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserService
{
	/**
	 * Create a new user and assign a role.
	 *
	 * @param array $data
	 * @return array
	 */
	public function createUser(array $data)
	{
		// Validate the incoming data
		$validator = $this->validateUserData($data);
		if ($validator->fails()) {
			return [
				'status' => false,
				'message' => $validator->errors()->first(),
			];
		}

		try {
			DB::beginTransaction();

			// Create user and assign roles
			$user = $this->createNewUser($data);
			$roleId = $this->assignRoleToUser($user, $data['role_id']);

			if (!$roleId) {
				DB::rollBack();
				return ['status' => false, 'message' => User::getMessage('common', 'invalid_request')];
			}

			// Commit the transaction
			DB::commit();

			return ['status' => true, 'message' => User::getMessage('user', 'create_success'), 'user' => $user];
		} catch (\Exception $e) {
			// Rollback the transaction if there's an exception
			DB::rollBack();
			return ['status' => false, 'message' => User::getMessage('user', 'create_failed')];
		}
	}

	/**
	 * Update an existing user and role.
	 *
	 * @param array $data
	 * @return array
	 */
	public function updateUser(array $data)
	{
		$user_id = User::decrypt($data['id']);
		$validator = $this->validateUserData($data, $user_id);

		if ($validator->fails()) {
			return [
				'status' => false,
				'message' => $validator->errors()->first(),
			];
		}

		try {
			$user = User::findOrFail($user_id);
			$user->update($data);
			
			if (isset($data['role_id'])) {
				$roleId = $this->assignRoleToUser($user, $data['role_id']);
				if (!$roleId) {
					return ['status' => false, 'message' => User::getMessage('common', 'invalid_request')];
				}
			}

			return ['status' => true, 'message' => User::getMessage('user', 'update_success'), 'user' => $user];
		} catch (\Exception $e) {
			return ['status' => false, 'message' => User::getMessage('user', 'update_failed')];
		}
	}

	/**
	 * Delete a user.
	 *
	 * @param int $id
	 * @return array
	 */
	public function deleteUser($id)
	{
		try {
			$user_id = User::decrypt($id);
			$user = User::findOrFail($user_id);
			$user->roles()->detach();
			$user->delete();

			return ['status' => true, 'message' => User::getMessage('user', 'delete_success')];
		} catch (\Exception $e) {
			return ['status' => false, 'message' => User::getMessage('user', 'delete_failed')];
		}
	}

	/**
	 * Retrieve all users.
	 *
	 * @return array
	 */
	public function getAllUsers()
	{
		try {
			$users = User::with('roles')->get();

			// Format response to include encrypted IDs
			$final = $users->map(function (User $user) {
				return $this->formatUserResponse($user);
			});

			return ['status' => true, 'users' => $final];
		} catch (\Exception $e) {
			return ['status' => false, 'message' => User::getMessage('user', 'fetch_failed')];
		}
	}

	/**
	 * Centralized validation logic for creating and updating users.
	 *
	 * @param array $data - Input data to be validated.
	 * @param int|null $userId - The ID of the user to exclude from unique checks (optional).
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validateUserData(array $data, $userId = null)
	{
		return Validator::make($data, [
			'id' => 'sometimes|string',  // Required when updating
			'name' => 'required|string|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $userId,
			'password' => 'sometimes|min:8|max:30',  // Optional when updating
			'role_id' => 'required|string',  // Always required
		]);
	}

	/**
	 * Helper function to create a new user.
	 *
	 * @param array $data
	 * @return \App\Models\User
	 */
	protected function createNewUser(array $data)
	{
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => Hash::make($data['password']),
		]);
	}

	/**
	 * Helper function to assign roles to a user.
	 *
	 * @param \App\Models\User $user
	 * @param string $encryptedRoleId
	 * @return void
	 */
	protected function assignRoleToUser(User $user, string $encryptedRoleId)
	{
		$roleId = UserRole::decrypt($encryptedRoleId);
		if ($roleId && $this->validateRoleExists($roleId)) {
			$user->roles()->sync($roleId);
		}
	}

	/**
	 * Validate if the given role ID exists in the user_roles table.
	 *
	 * @param int $roleId
	 * @return bool
	 */
	protected function validateRoleExists(int $roleId): bool
	{
		return UserRole::where('id', $roleId)->exists();
	}

	/**
	 * Format user response to ensure encrypted IDs for frontend.
	 *
	 * @param \App\Models\User $user
	 * @return array
	 */
	protected function formatUserResponse(User $user)
	{
		$user->id = User::encrypt($user->id);

		// Encrypt role IDs within the response
		if ($user->roles) {
			$user->roles = $user->roles->map(function ($role) {
				return [
					'id' => UserRole::encrypt($role->id),
					'name' => $role->name,
					'description' => $role->description,
				];
			});
		}

		return $user->toArray();
	}
}