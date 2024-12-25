<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\WorkWeek;
use Carbon\Carbon;

class TimesheetController extends Controller
{
	/**
	 * Display a list of timesheets with search and filter capabilities.
	 */
	public function index(Request $request)
	{
		$status = $request->query('status');  
		$userId = Timesheet::decrypt($request->query('user_id'));
		$search = $request->query('search');
		$limit = $request->query('limit', 10);

		$query = Timesheet::with('user');

		if ($status) {
			$query->where('status', $status);
		}

		if ($userId) {
			$query->where('user_id', $userId);
		}

		if ($search) {
			$searchTerms = explode(',', $search);
			$searchTerms = array_map('trim', $searchTerms);

			$query->where(function ($q) use ($searchTerms) {
				foreach ($searchTerms as $term) {
					$q->orWhereHas('user', function ($query) use ($term) {
						$query->where('first_name', 'LIKE', '%' . $term . '%')
							->orWhere('last_name', 'LIKE', '%' . $term . '%');
					});
				}
			});
		}

		$timesheets = $query->paginate($limit);

		return response()->json(['message' => Timesheet::getMessage('timesheets', 'fetch_success'), 'data' => $timesheets], 200);
	}

	/**
	 * Clock-in for the user.
	 */
	public function clockIn(Request $request)
	{
		$request->validate([
			'user_id' => 'required|string',
			'entry_date' => 'required|date',
			'clock_in_time' => 'required|date_format:H:i'
		]);

		$userId = Timesheet::decrypt($request->user_id);

		if (!$userId) {
			return response()->json(['error' => Timesheet::getMessage('common', 'invalid_request')], 400);
		}

		$timesheet = Timesheet::create([
			'user_id' => $userId,
			'entry_date' => $request->entry_date,
			'clock_in_time' => $request->clock_in_time,
			'status' => 'approved',
		]);

		return response()->json(['message' => Timesheet::getMessage('timesheets', 'create_success'), 'data' => $timesheet], 201);
	}

	/**
	 * Clock-out for the user.
	 */
	public function clockOut(Request $request, $id)
	{
		$timesheetId = Timesheet::decrypt($id);

		if (!$timesheetId) {
			return response()->json(['error' => Timesheet::getMessage('common', 'invalid_request')], 400);
		}

		$request->validate([
			'clock_out_time' => 'required|date_format:H:i'
		]);

		$timesheet = Timesheet::find($timesheetId);
		if (!$timesheet) {
			return response()->json(['message' => Timesheet::getMessage('timesheets', 'not_found')], 404);
		}

		$workWeek = WorkWeek::where('day', Carbon::parse($timesheet->entry_date)->format('l'))->first();
		$clockOutTime = $request->clock_out_time ?? $workWeek->default_end_time;

		$timesheet->update([
			'clock_out_time' => $clockOutTime,
			'status' => 'approved'
		]);

		return response()->json(['message' => Timesheet::getMessage('timesheets', 'update_success'), 'data' => $timesheet], 200);
	}

	/**
	 * Store a manually created timesheet entry.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'user_id' => 'required|string',
			'entry_date' => 'required|date',
			'clock_in_time' => 'required|date_format:H:i',
			'clock_out_time' => 'required|date_format:H:i|after:clock_in_time',
			'message' => 'nullable|string',
		]);

		$userId = Timesheet::decrypt($request->user_id);

		if (!$userId) {
			return response()->json(['error' => Timesheet::getMessage('common', 'invalid_request')], 400);
		}

		$timesheet = Timesheet::create([
			'user_id' => $userId,
			'entry_date' => $request->entry_date,
			'clock_in_time' => $request->clock_in_time,
			'clock_out_time' => $request->clock_out_time,
			'status' => 'pending',
			'message' => $request->message,
		]);

		return response()->json(['message' => Timesheet::getMessage('timesheets', 'create_success'), 'data' => $timesheet], 201);
	}

	/**
	 * Update an existing timesheet entry.
	 */
	public function update(Request $request, $id)
	{
		$timesheetId = Timesheet::decrypt($id);

		if (!$timesheetId) {
			return response()->json(['error' => Timesheet::getMessage('common', 'invalid_request')], 400);
		}

		$timesheet = Timesheet::find($timesheetId);
		if (!$timesheet) {
			return response()->json(['message' => Timesheet::getMessage('timesheets', 'not_found')], 404);
		}

		$request->validate([
			'clock_in_time' => 'sometimes|date_format:H:i',
			'clock_out_time' => 'sometimes|date_format:H:i|after:clock_in_time',
			'message' => 'nullable|string',
		]);

		$timesheet->update(array_merge($request->all(), ['status' => 'pending']));

		return response()->json(['message' => Timesheet::getMessage('timesheets', 'update_success'), 'data' => $timesheet], 200);
	}

	/**
	 * Delete a timesheet entry.
	 */
	public function destroy($id)
	{
		$timesheetId = Timesheet::decrypt($id);

		if (!$timesheetId) {
			return response()->json(['error' => Timesheet::getMessage('common', 'invalid_request')], 400);
		}

		$timesheet = Timesheet::find($timesheetId);

		if (!$timesheet) {
			return response()->json(['message' => Timesheet::getMessage('timesheets', 'not_found')], 404);
		}

		$timesheet->delete();

		return response()->json(['message' => Timesheet::getMessage('timesheets', 'delete_success')], 200);
	}
}