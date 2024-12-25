<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$limit = $request->query('limit', 10);  // Default to 10 items per page if not specified
		$hireDate = $request->query('hire_date');
		$search = $request->query('search');
		$sortBy = $request->query('sort_by', 'created_at');  // Default sorting by created_at
		$sortOrder = $request->query('sort_order', 'DESC');  // Default sorting order is descending
		
		// Build the query with optional filters
		$query = Employee::query();
		$searchableFields = ['first_name', 'last_name', 'department', 'phone_number', 'email'];

		if ($hireDate) {
			$query->whereDate('hire_date', $hireDate);
		}

		if ($search) {
			// Split the search string by commas
			$searchTerms = explode(',', $search);
			$searchTerms = array_map("trim",  $searchTerms);  // Remove leading/trailing whitespace from each term
			$last = count($searchTerms) -1;

			foreach ($searchTerms as $index => $term) {
				$query->where(function($q) use ($term, $searchableFields, $index, $searchTerms, $last) {
					foreach ($searchableFields as $field) {
						if($index==$last)
							$q->orWhere($field, 'LIKE', '%' . $term . '%');
						else
							$q->orWhere($field, $term);
					}
				});
			}
		}

		$query->orderBy($sortBy, $sortOrder);
		$employees = $query->paginate($limit);
		return response()->json(['message' => Employee::getMessage('user', 'fetch_success'), 'data' => $employees], 200);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'first_name' => 'required|string',
			'last_name' => 'required|string',
			'email' => 'required|email|unique:employees',
			'phone_number' => 'nullable|string',
			'department' => 'required|string',
			'hire_date' =>	'required|date'
		]);
		
		$employee = Employee::create($request->all());
		return response()->json(['message' => Employee::getMessage('user', 'create_success'), 'data' => $employee], 201);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $id)
	{
		$employee = Employee::find($id);
		if (!$employee) {
			return response()->json(['message' => Employee::getMessage('user', 'not_found')], 404);
		}
		return response()->json(['message' => Employee::getMessage('user', 'fetch_success'), 'data' => $employee], 200);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $id)
	{
		$employee = Employee::find($id);
		if (!$employee) {
			return response()->json(['message' => Employee::getMessage('user', 'not_found')], 404);
		}
		$employee->update($request->all());
		return response()->json(['message' => Employee::getMessage('user', 'update_success'), 'data' => $employee], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		$employee = Employee::destroy($id);
		if (!$employee) {
			return response()->json(['message' => Employee::getMessage('user', 'not_found')], 404);
		}
		return response()->json(['message' => Employee::getMessage('user', 'delete_success')], 200);
	}
}