<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\Expense;
use App\Models\Timesheet;

class DashboardController extends Controller
{
	/**
	 * Display the dashboard based on the user role and permissions.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		// Get the authenticated user and ensure they are loaded with roles
		$user = Employee::getAuthenticatedUser();
		$user->load('roles');

		// Employee Summary Data for Admin and HR
		$employeeSummary = ($user->hasRole('Admin') || $user->hasRole('HR')) 
							? Employee::all() 
							: [];

		// Leave Requests Data for Admin, HR, and Manager
		$leaveRequests = ($user->hasRole('Admin') || $user->hasRole('HR') || $user->hasRole('Manager')) 
							? Leave::where('status', 'pending')->get() 
							: [];

		// Expense Overview Data for Admin and Finance
		$expenseOverview = ($user->hasRole('Admin') || $user->hasRole('Finance')) 
							? Expense::where('status', 'pending')->get() 
							: [];

		// Timesheet Data for Admin, HR, and Manager
		$timesheets = ($user->hasRole('Admin') || $user->hasRole('HR') || $user->hasRole('Manager')) 
							? Timesheet::where('status', 'pending')->get() 
							: [];

		return view('dashboard', compact('employeeSummary', 'leaveRequests', 'expenseOverview', 'timesheets'));
	}
}