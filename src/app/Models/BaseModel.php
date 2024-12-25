<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
	use HasFactory;

	// Encrypt role IDs when accessed
	public static function encrypt($id)
	{
		return Crypt::encryptString($id);
	}

	public static function decrypt($id)
	{
		try {
			return Crypt::decryptString($id);
		}
		catch (\Exception $e)
		{
			return null; // Return null if decryption fails
		}
	}

	protected static $messages = [
		// Success messages for User management
		'user' => [
			'create_success' => 'User has been successfully created!',
			'update_success' => 'User details have been successfully updated!',
			'delete_success' => 'User has been deleted successfully!',
			'fetch_success' => 'User data retrieved successfully!',
			'not_found' => 'The specified user does not exist or could not be found.',
			'create_failed' => 'Failed to create the user. Please check the provided details and try again.',
			'update_failed' => 'Failed to update user details. Ensure the information is correct and try again.',
			'delete_failed' => 'Failed to delete the user. The user may be associated with active records.',
		],

		// Success and error messages for Leave management
		'leaves' => [
			'create_success' => 'Leave request submitted successfully!',
			'create_success_overlimit' => 'Leave request submitted! Note: The requested days exceed the available leave balance.',
			'update_success' => 'Leave request updated successfully!',
			'delete_success' => 'Leave request deleted successfully!',
			'fetch_success' => 'Leave requests retrieved successfully!',
			'not_found' => 'The specified leave request could not be found.',
			'not_allocated' => 'Leave type not allocated to the user. Please check your leave entitlements.',
			'create_failed' => 'Failed to submit leave request. Please ensure all details are correct.',
			'update_failed' => 'Failed to update leave request. Please verify the information and try again.',
			'delete_failed' => 'Failed to delete leave request. The request may have already been processed.',
			'approval_failed' => 'Failed to process leave approval. Please try again or contact support.',
		],

		// Success and error messages for Expense management
		'expenses' => [
			'create_success' => 'Expense record has been successfully created!',
			'update_success' => 'Expense record updated successfully!',
			'delete_success' => 'Expense record deleted successfully!',
			'fetch_success' => 'Expense data retrieved successfully!',
			'not_found' => 'The specified expense record could not be found.',
			'create_failed' => 'Failed to create the expense record. Please review the provided details and try again.',
			'update_failed' => 'Failed to update the expense record. Please check the information and try again.',
			'delete_failed' => 'Failed to delete the expense record. The record may be linked to other resources.',
			'approval_failed' => 'Expense approval could not be processed. Please try again later.',
		],

		// Success and error messages for Timesheet management
		'timesheets' => [
			'create_success' => 'Timesheet entry created successfully!',
			'update_success' => 'Timesheet entry updated successfully!',
			'delete_success' => 'Timesheet entry deleted successfully!',
			'fetch_success' => 'Timesheet data retrieved successfully!',
			'not_found' => 'The specified timesheet entry could not be found.',
			'create_failed' => 'Failed to create the timesheet entry. Please review the details and try again.',
			'update_failed' => 'Failed to update the timesheet entry. Please check the data and try again.',
			'delete_failed' => 'Failed to delete the timesheet entry. The record may already be removed.',
			'approval_failed' => 'Timesheet approval failed. Ensure all fields are correctly filled.',
		],

		// Common error and status messages
		'common' => [
			'invalid_request' => 'The request could not be processed due to invalid data. Please verify and try again.',
			'unauthorized' => 'You do not have the required permissions to access this resource.',
			'forbidden' => 'Access to this resource is forbidden.',
			'server_error' => 'An internal server error occurred. Please try again later.',
			'unknown_error' => 'An unknown error occurred. Please contact support if the issue persists.',
		]
	];

	/**
	 * Retrieve a message by key.
	 *
	 * @param string $type
	 * @param string $key
	 * @return string
	 */
	public static function getMessage($type, $key)
	{
		return self::$messages[$type][$key] ?? 'An unknown error occurred.';
	}

	/**
	 * Get the authenticated user and ensure they're loaded with roles.
	 * Redirect to login if not authenticated.
	 * 
	 * @return \App\Models\User|null
	 */
	public static function getAuthenticatedUser()
	{
		if (!Auth::check()) {
			// Redirect to login if the user is not authenticated
			return redirect()->route('login');
		}

		// Get the authenticated user and eager load roles
		$user = Auth::user();

		return $user;
	}
}