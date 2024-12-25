<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
	public function run(): void
	{
		// Insert roles individually to ensure separate entries
		DB::table("user_roles")->insert([
			['name' => "Admin", "description" => "Full access to the system"],
			['name' => "Manager", "description" => "Manage employees assigned to them and view reports"],
			['name' => "Employee", "description" => "View own data and submit requests"],
			['name' => "HR", "description" => "Manage Employee Data, Leave Types, and Expense Types"],
			['name' => "Finance", "description" => "Manage Expenses and Approvals"],
		]);

		// Insert permissions individually to ensure separate entries
		DB::table("user_permissions")->insert([
			// Employee Management
			['name' => "view_employee", 'api_route' => "/employees", "description" => "View employee data"],
			['name' => "add_employee", 'api_route' => "/employees", "description" => "Add employee data"],
			['name' => "edit_employee", 'api_route' => "/employees/{id}", "description" => "Edit employee data"],
			['name' => "delete_employee", 'api_route' => "/employees/{id}", "description" => "Delete employee data"],
			
			// Leave Management
			['name' => "view_leaves", 'api_route' => "/leaves", "description" => "View leave requests"],
			['name' => "add_leaves", 'api_route' => "/leaves", "description" => "Add leave requests"],
			['name' => "edit_leaves", 'api_route' => "/leaves/{id}", "description" => "Edit leave requests"],
			['name' => "delete_leaves", 'api_route' => "/leaves/{id}", "description" => "Delete leave requests"],
			['name' => "approve_leaves", 'api_route' => "/leaves/approve/{id}", "description" => "Approve leave requests"],
			
			// Leave Type Management
			['name' => "view_leave_types", 'api_route' => "/leave-types", "description" => "View all leave types"],
			['name' => "add_leave_types", 'api_route' => "/leave-types", "description" => "Create a new leave type"],
			['name' => "edit_leave_types", 'api_route' => "/leave-types/{id}", "description" => "Edit a leave type"],
			['name' => "delete_leave_types", 'api_route' => "/leave-types/{id}", "description" => "Delete a leave type"],
			
			// Expense Management
			['name' => "view_expenses", 'api_route' => "/expenses", "description" => "View expense requests"],
			['name' => "add_expenses", 'api_route' => "/expenses", "description" => "Add expense requests"],
			['name' => "edit_expenses", 'api_route' => "/expenses/{id}", "description" => "Edit expense requests"],
			['name' => "delete_expenses", 'api_route' => "/expenses/{id}", "description" => "Delete expense requests"],
			['name' => "approve_expenses", 'api_route' => "/expenses/approve/{id}", "description" => "Approve expense requests"],
			
			// Expense Type Management
			['name' => "view_expense_types", 'api_route' => "/expense-types", "description" => "View all expense types"],
			['name' => "add_expense_types", 'api_route' => "/expense-types", "description" => "Create a new expense type"],
			['name' => "edit_expense_types", 'api_route' => "/expense-types/{id}", "description" => "Edit an expense type"],
			['name' => "delete_expense_types", 'api_route' => "/expense-types/{id}", "description" => "Delete an expense type"],

			// Timesheet Management
			['name' => "view_timesheets", 'api_route' => "/timesheets", "description" => "View timesheets"],
			['name' => "add_timesheets", 'api_route' => "/timesheets", "description" => "Add new timesheets"],
			['name' => "edit_timesheets", 'api_route' => "/timesheets/{id}", "description" => "Edit timesheets"],
			['name' => "delete_timesheets", 'api_route' => "/timesheets/{id}", "description" => "Delete timesheets"],
			['name' => "approve_timesheets", 'api_route' => "/timesheets/approve/{id}", "description" => "Approve timesheets"],

			// Work Week Management
			['name' => "view_work_weeks", 'api_route' => "/work-weeks", "description" => "View work week settings"],
			['name' => "edit_work_weeks", 'api_route' => "/work-weeks", "description" => "Edit work week settings"],
		]);

		// Insert Users (seeding with example data)
		DB::table('users')->insert([
			['name' => 'Jane Doe', 'email' => 'admin@example.com', 'password' => Hash::make('password')],
			['name' => 'John Doe', 'email' => 'manager@example.com', 'password' => Hash::make('password')],
			['name' => 'Jennifer Day', 'email' => 'employee@example.com', 'password' => Hash::make('password')],
			['name' => 'Jane Day', 'email' => 'hr@example.com', 'password' => Hash::make('password')],
			['name' => 'John Day', 'email' => 'finance@example.com', 'password' => Hash::make('password')],
		]);

		// Get role IDs for further assignment
		$adminRoleId = DB::table('user_roles')->where('name', 'Admin')->value('id');
		$employeeRoleId = DB::table('user_roles')->where('name', 'Employee')->value('id');
		$managerRoleId = DB::table('user_roles')->where('name', 'Manager')->value('id');
		$hrRoleId = DB::table('user_roles')->where('name', 'HR')->value('id');
		$financeRoleId = DB::table('user_roles')->where('name', 'Finance')->value('id');

		// Get user IDs
		$adminUserId = DB::table('users')->where('email', 'admin@example.com')->value('id');
		$managerUserId = DB::table('users')->where('email', 'manager@example.com')->value('id');
		$employeeUserId = DB::table('users')->where('email', 'employee@example.com')->value('id');
		$hrUserId = DB::table('users')->where('email', 'hr@example.com')->value('id');
		$financeUserId = DB::table('users')->where('email', 'finance@example.com')->value('id');

		// Map users to roles
		DB::table('user_role_mapping')->insert([
			['user_id' => $adminUserId, 'role_id' => $adminRoleId],
			['user_id' => $managerUserId, 'role_id' => $managerRoleId],
			['user_id' => $employeeUserId, 'role_id' => $employeeRoleId],
			['user_id' => $hrUserId, 'role_id' => $hrRoleId],
			['user_id' => $financeUserId, 'role_id' => $financeRoleId],
		]);
	}
}