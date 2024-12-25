<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'permission'])->group(function () {
	// User Management Routes
	Route::get('/users', [UserController::class, 'index']);  
	Route::post('/users', [UserController::class, 'store']);
	Route::put('/users/{id}', [UserController::class, 'update']);
	Route::delete('/users/{id}', [UserController::class, 'destroy']);

	// Employee Management Routes
	Route::get('/employees', [EmployeeController::class, 'index']);
	Route::get('/employees/{id}', [EmployeeController::class, 'show']);
	Route::post('/employees', [EmployeeController::class, 'store']);
	Route::put('/employees/{id}', [EmployeeController::class, 'update']);
	Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

	// Leave Management Routes
	Route::get('/leaves', [LeaveController::class, 'index']);
	Route::post('/leaves', [LeaveController::class, 'store']);
	Route::put('/leaves/{id}', [LeaveController::class, 'update']);
	Route::delete('/leaves/{id}', [LeaveController::class, 'destroy']);
	Route::put('/leaves/approve/{id}', [LeaveController::class, 'manage']);
	Route::get('/leave-types', [LeaveController::class, 'getLeaveTypes']);  // Fetch leave types
	Route::post('/leave-types', [LeaveController::class, 'storeLeaveType']);
	Route::put('/leave-types/{id}', [LeaveController::class, 'updateLeaveType']);
	Route::delete('/leave-types/{id}', [LeaveController::class, 'deleteLeaveType']);

	// Expenses Management Routes
	Route::get('/expenses', [ExpenseController::class, 'index']);
	Route::post('/expenses', [ExpenseController::class, 'store']);
	Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
	Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
	Route::put('/expenses/approve/{id}', [ExpenseController::class, 'manage']);
	Route::get('/expense-types', [ExpenseController::class, 'getExpenseTypes']);  // Fetch expense types
	Route::post('/expense-types', [ExpenseController::class, 'storeExpenseType']);
	Route::put('/expense-types/{id}', [ExpenseController::class, 'updateExpenseType']);
	Route::delete('/expense-types/{id}', [ExpenseController::class, 'deleteExpenseType']);

	// Timesheet Management Routes
	Route::get('/timesheets', [TimesheetController::class, 'index']);
	Route::post('/timesheets', [TimesheetController::class, 'store']);
	Route::put('/timesheets/{id}', [TimesheetController::class, 'update']);
	Route::delete('/timesheets/{id}', [TimesheetController::class, 'destroy']);
	Route::put('/timesheets/approve/{id}', [TimesheetController::class, 'manage']);
	Route::post('/timesheets/clock-in', [TimesheetController::class, 'clockIn']);
	Route::post('/timesheets/clock-out', [TimesheetController::class, 'clockOut']);
});