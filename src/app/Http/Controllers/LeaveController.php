<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\LeaveUserMapping;
use Carbon\Carbon;

class LeaveController extends Controller
{
	/**
	 * Display a list of leave requests with search and filter capabilities.
	 */
	public function index(Request $request)
	{
		$status = $request->query('status');
		$userId = Leave::decrypt($request->query('user_id'));
		$leaveType = Leave::decrypt($request->query('leave_type_id'));
		$search = $request->query('search');
		$limit = $request->query('limit', 10);

		$query = Leave::with('user', 'leaveType');

		if ($status) {
			$query->where('status', $status);
		}

		if ($userId) {
			$query->where('user_id', $userId);
		}

		if ($leaveType) {
			$query->where('leave_type_id', $leaveType);
		}

		if ($search) {
			$searchTerms = explode(',', $search);
			$searchTerms = array_map('trim', $searchTerms);

			$query->where(function ($q) use ($searchTerms) {
				foreach ($searchTerms as $term) {
					$q->orWhereHas('user', function ($query) use ($term) {
						$query->where('first_name', 'LIKE', '%' . $term . '%')
							->orWhere('last_name', 'LIKE', '%' . $term . '%');
					})
					->orWhereHas('leaveType', function ($query) use ($term) {
						$query->where('name', 'LIKE', '%' . $term . '%');
					})
					->orWhereHas('leave_requests', function ($query) use ($term) {
						$query->where('name', 'LIKE', '%' . $term . '%');
					});
				}
			});
		}

		$leaves = $query->paginate($limit);

		return response()->json(['message' => Leave::getMessage('leaves', 'fetch_success'), 'data' => $leaves], 200);
	}

	/**
	 * Store a newly created leave request.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'user_id' => 'required|string',
			'leave_type_id' => 'required|string',
			'start_date' => 'required|date',
			'end_date' => 'required|date|after_or_equal:start_date',
			'message' => 'nullable|string',
		]);

		$userId = Leave::decrypt($request->user_id);
		$leaveTypeId = Leave::decrypt($request->leave_type_id);

		if (!$userId || !$leaveTypeId) {
			return response()->json(['error' => Leave::getMessage('common', 'invalid_request')], 400);
		}

		$totalDays = $this->calculateTotalDays($request->start_date, $request->end_date);

		$availabilityCheck = $this->checkLeaveAvailability($userId, $leaveTypeId, $totalDays);
		$message_key = $availabilityCheck['message_key'];

		$leave = Leave::create([
			'user_id' => $userId,
			'leave_type_id' => $leaveTypeId,
			'start_date' => $request->start_date,
			'end_date' => $request->end_date,
			'total_days' => $totalDays,
			'status' => 'pending',
			'message' => $request->message,
		]);

		return response()->json(['message' => Leave::getMessage('leaves', $message_key), 'data' => $leave], 201);
	}

	/**
	 * Update an existing leave request.
	 */
	public function update(Request $request, $id)
	{
		$leaveId = Leave::decrypt($id);

		if (!$leaveId) {
			return response()->json(['error' => Leave::getMessage('common', 'invalid_request')], 400);
		}

		$leave = Leave::find($leaveId);
		if (!$leave) {
			return response()->json(['message' => Leave::getMessage('leaves', 'not_found')], 404);
		}

		$request->validate([
			'start_date' => 'sometimes|date',
			'end_date' => 'sometimes|date|after_or_equal:start_date',
			'message' => 'nullable|string',
		]);

		$totalDays = $this->calculateTotalDays($request->start_date ?? $leave->start_date, $request->end_date ?? $leave->end_date);
		$leave->update(array_merge($request->all(), ['total_days' => $totalDays, 'status' => 'pending']));

		return response()->json(['message' => Leave::getMessage('leaves', 'update_success'), 'data' => $leave], 200);
	}

	/**
	 * Delete an existing leave request.
	 */
	public function destroy($id)
	{
		$leaveId = Leave::decrypt($id);

		if (!$leaveId) {
			return response()->json(['error' => Leave::getMessage('common', 'invalid_request')], 400);
		}

		$leave = Leave::find($leaveId);

		if (!$leave) {
			return response()->json(['message' => Leave::getMessage('leaves', 'not_found')], 404);
		}

		$leave->delete();

		return response()->json(['message' => Leave::getMessage('leaves', 'delete_success')], 200);
	}

	/**
	 * Manage a leave request (approve or reject).
	 */
	public function manage(Request $request, $id)
	{
		$leaveId = Leave::decrypt($id);
		if (!$leaveId) {
			return response()->json(['error' => Leave::getMessage('common', 'invalid_request')], 400);
		}

		$leave = Leave::find($leaveId);
		if (!$leave) {
			return response()->json(['message' => Leave::getMessage('leaves', 'not_found')], 404);
		}

		if ($leave->status !== 'pending') {
			return response()->json(['message' => Leave::getMessage('leaves', 'already_processed')], 400);
		}

		$request->validate([
			'approver_user_id' => 'required|string',
			'status' => 'required|string|in:approved,rejected',
			'approver_message' => 'nullable|string',
		]);

		$approverId = Leave::decrypt($request->approver_user_id);
		if (!$approverId) {
			return response()->json(['error' => Leave::getMessage('common', 'invalid_request')], 400);
		}

		$leave = Leave::find($leaveId);
		if (!$leave) {
			return response()->json(['message' => Leave::getMessage('leaves', 'not_found')], 404);
		}

		$leave->update([
			'status' => $request->status,
			'approver_user_id' => $approverId,
			'approver_date' => now(),
			'approver_message' => $request->approver_message,
		]);

		return response()->json(['message' => Leave::getMessage('leaves', 'update_success'), 'data' => $leave], 200);
	}

	/**
	 * Get a list of leave types with search functionality.
	 */
	public function getLeaveTypes(Request $request)
	{
		$search = $request->query('search');
		$limit = $request->query('limit', 10);

		$query = LeaveType::query();

		if ($search) {
			$query->where('name', 'LIKE', '%' . $search . '%');
		}

		$leaveTypes = $query->paginate($limit);

		return response()->json(['message' => Leave::getMessage('leaves', 'fetch_success'), 'data' => $leaveTypes], 200);
	}

	/**
	 * Calculate the total days between start and end dates, excluding weekends.
	 */
	protected function calculateTotalDays($startDate, $endDate)
	{
		$start = Carbon::parse($startDate);
		$end = Carbon::parse($endDate);
		$totalDays = 0;

		while ($start->lte($end)) {
			if (!$start->isWeekend()) {
				$totalDays++;
			}
			$start->addDay();
		}

		return $totalDays;
	}

	/**
	 * Check if the requested leave days are available for the user.
	 */
	protected function checkLeaveAvailability($userId, $leaveTypeId, $totalDays)
	{
		// Retrieve the allocated leaves for the user and leave type
		$leaveMapping = LeaveUserMapping::where('user_id', $userId)->where('leave_type_id', $leaveTypeId)->first();

		if (!$leaveMapping) {
			return ['status' => false, 'message_key' => LeaveUserMapping::getMessage('leaves', 'not_allocated')];
		}

		// Get approved leave days excluding non-working days based on the work week
		$approvedLeaveDays = $this->calculateApprovedLeaveDays($userId, $leaveTypeId);

		// Calculate remaining leaves after accounting for approved leaves
		$remainingLeaves = $leaveMapping->allocated_leaves - $approvedLeaveDays;

		// Determine if the requested leave days exceed the remaining allocated leaves
		$message_key = $totalDays <= $remainingLeaves
			? 'create_success'
			: 'create_success_overlimit';

		return ['status' => true, 'message_key' => $message_key];
	}

	/**
	 * Calculate the number of working days between two dates, excluding weekends and non-working days.
	 */
	protected function calculateWorkingDays($startDate, $endDate, $workWeek)
	{
		$start = Carbon::parse($startDate);
		$end = Carbon::parse($endDate);
		$workingDays = 0;

		while ($start->lte($end)) {
			$dayName = $start->format('l'); // Get the day name (e.g., 'Monday')
			
			// Check if the day is a working day based on the work week configuration
			if (isset($workWeek->$dayName) && $workWeek->$dayName['is_working_day']) {
				$workingDays++;
			}
			$start->addDay();
		}

		return $workingDays;
	}

	/**
	 * Store a new leave type.
	 */
	public function storeLeaveType(Request $request)
	{
		$request->validate([
			'name' => 'required|string|unique:leave_types',
			'default_value' => 'required|integer|min:0',
			'description' => 'nullable|string',
			'is_active' => 'required|integer|in:0,1,2',  // 0: deleted, 1: active, 2: inactive
		]);

		$leaveType = LeaveType::create($request->all());

		return response()->json(['message' => LeaveType::getMessage('leaves', 'create_success'), 'data' => $leaveType], 201);
	}

	/**
	 * Update an existing leave type.
	 */
	public function updateLeaveType(Request $request, $id)
	{
		$leaveTypeId = Leave::decrypt($id);

		if (!$leaveTypeId) {
			return response()->json(['error' => Leave::getMessage('common', 'invalid_request')], 400);
		}

		$leaveType = LeaveType::find($leaveTypeId);
		if (!$leaveType) {
			return response()->json(['message' => LeaveType::getMessage('leaves', 'not_found')], 404);
		}

		$request->validate([
			'name' => 'sometimes|string|unique:leave_types,name,' . $leaveTypeId,
			'default_value' => 'sometimes|integer|min:0',
			'description' => 'nullable|string',
			'is_active' => 'sometimes|integer|in:0,1,2',
		]);

		$leaveType->update($request->all());

		return response()->json(['message' => LeaveType::getMessage('leaves', 'update_success'), 'data' => $leaveType], 200);
	}

	/**
	 * Delete a leave type.
	 */
	public function deleteLeaveType($id)
	{
		$leaveTypeId = Leave::decrypt($id);

		if (!$leaveTypeId) {
			return response()->json(['error' => Leave::getMessage('common', 'invalid_request')], 400);
		}

		$leaveType = LeaveType::find($leaveTypeId);

		if (!$leaveType) {
			return response()->json(['message' => LeaveType::getMessage('leaves', 'not_found')], 404);
		}

		$leaveType->update(['is_active' => 0]);  // Soft delete by setting status to 0 (Deleted)

		return response()->json(['message' => LeaveType::getMessage('leaves', 'delete_success')], 200);
	}
}