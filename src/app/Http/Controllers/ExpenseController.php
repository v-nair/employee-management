<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseType;

class ExpenseController extends Controller
{
	/**
	 * Display a list of expense requests with search and filter capabilities.
	 */
	public function index(Request $request)
	{
		$status = $request->query('status');  // Filter by status
		$userId = Expense::decrypt($request->query('user_id'));  // Filter by user ID
		$expenseTypeId = Expense::decrypt($request->query('expense_type_id'));  // Filter by expense type ID
		$search = $request->query('search');  // Search query string
		$limit = $request->query('limit', 10);  // Default to 10 items per page if not specified

		$query = Expense::with('user', 'expenseType');

		// Apply filters
		if ($status) {
			$query->where('status', $status);
		}

		if ($userId) {
			$query->where('user_id', $userId);
		}

		if ($expenseTypeId) {
			$query->where('expense_type_id', $expenseTypeId);
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
					->orWhereHas('expenseType', function ($query) use ($term) {
						$query->where('name', 'LIKE', '%' . $term . '%');
					});
				}
			});
		}

		$expenses = $query->paginate($limit);

		return response()->json(['message' => Expense::getMessage('expenses', 'fetch_success'), 'data' => $expenses], 200);
	}

	/**
	 * Store a newly created expense request.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'user_id' => 'required|string',
			'expense_type_id' => 'required|string',
			'amount' => 'required|numeric|min:0',
			'description' => 'nullable|string',
		]);

		$userId = Expense::decrypt($request->user_id);
		$expenseTypeId = Expense::decrypt($request->expense_type_id);

		if (!$userId || !$expenseTypeId) {
			return response()->json(['error' => Expense::getMessage('common', 'invalid_request')], 400);
		}

		$expense = Expense::create([
			'user_id' => $userId,
			'expense_type_id' => $expenseTypeId,
			'amount' => $request->amount,
			'description' => $request->description,
			'status' => 'pending',
		]);

		return response()->json(['message' => Expense::getMessage('expenses', 'create_success'), 'data' => $expense], 201);
	}

	/**
	 * Update an existing expense request.
	 */
	public function update(Request $request, $id)
	{
		$expenseId = Expense::decrypt($id);

		if (!$expenseId) {
			return response()->json(['error' => Expense::getMessage('common', 'invalid_request')], 400);
		}

		$expense = Expense::find($expenseId);
		if (!$expense) {
			return response()->json(['message' => Expense::getMessage('expenses', 'not_found')], 404);
		}

		$request->validate([
			'amount' => 'sometimes|numeric|min:0',
			'description' => 'nullable|string',
		]);

		$expense->update($request->all());

		return response()->json(['message' => Expense::getMessage('expenses', 'update_success'), 'data' => $expense], 200);
	}

	/**
	 * Delete an existing expense request.
	 */
	public function destroy($id)
	{
		$expenseId = Expense::decrypt($id);

		if (!$expenseId) {
			return response()->json(['error' => Expense::getMessage('common', 'invalid_request')], 400);
		}

		$expense = Expense::find($expenseId);

		if (!$expense) {
			return response()->json(['message' => Expense::getMessage('expenses', 'not_found')], 404);
		}

		$expense->delete();

		return response()->json(['message' => Expense::getMessage('expenses', 'delete_success')], 200);
	}

	/**
	 * Manage an expense request (approve or reject).
	 */
	public function manage(Request $request, $id)
	{
		$expenseId = Expense::decrypt($id);
		if (!$expenseId) {
			return response()->json(['error' => Expense::getMessage('common', 'invalid_request')], 400);
		}

		$expense = Expense::find($expenseId);
		if (!$expense) {
			return response()->json(['message' => Expense::getMessage('expenses', 'not_found')], 404);
		}

		if ($expense->status === 'approved') {
			return response()->json(['message' => Expense::getMessage('expenses', 'already_processed')], 400);
		}

		$request->validate([
			'approver_user_id' => 'required|string',
			'status' => 'required|string|in:approved,rejected',
			'approver_message' => 'nullable|string',
		]);

		$approverId = Expense::decrypt($request->approver_user_id);
		if (!$approverId) {
			return response()->json(['error' => Expense::getMessage('common', 'invalid_request')], 400);
		}

		$expense = Expense::find($expenseId);
		if (!$expense) {
			return response()->json(['message' => Expense::getMessage('expenses', 'not_found')], 404);
		}

		$expense->update([
			'status' => $request->status,
			'approver_user_id' => $approverId,
			'approver_message' => $request->approver_message,
		]);

		return response()->json(['message' => Expense::getMessage('expenses', 'update_success'), 'data' => $expense], 200);
	}

	/**
	 * Get a list of expense types with search functionality.
	 */
	public function getExpenseTypes(Request $request)
	{
		$search = $request->query('search');
		$limit = $request->query('limit', 10);

		$query = ExpenseType::query();

		if ($search) {
			$query->where('name', 'LIKE', '%' . $search . '%');
		}

		$expenseTypes = $query->paginate($limit);

		return response()->json(['message' => ExpenseType::getMessage('expenses', 'fetch_success'), 'data' => $expenseTypes], 200);
	}

	/**
	 * Store a new expense type.
	 */
	public function storeExpenseType(Request $request)
	{
		$request->validate([
			'name' => 'required|string|unique:expense_types',
			'description' => 'nullable|string',
			'is_active' => 'required|integer|in:0,1,2',  // 0: deleted, 1: active, 2: inactive
		]);

		$expenseType = ExpenseType::create($request->all());

		return response()->json(['message' => ExpenseType::getMessage('expenses', 'create_success'), 'data' => $expenseType], 201);
	}

	/**
	 * Update an existing expense type.
	 */
	public function updateExpenseType(Request $request, $id)
	{
		$expenseTypeId = Expense::decrypt($id);

		if (!$expenseTypeId) {
			return response()->json(['error' => Expense::getMessage('common', 'invalid_request')], 400);
		}

		$expenseType = ExpenseType::find($expenseTypeId);
		if (!$expenseType) {
			return response()->json(['message' => ExpenseType::getMessage('expenses', 'not_found')], 404);
		}

		$request->validate([
			'name' => 'sometimes|string|unique:expense_types,name,' . $expenseTypeId,
			'description' => 'nullable|string',
			'is_active' => 'sometimes|integer|in:0,1,2',
		]);

		$expenseType->update($request->all());

		return response()->json(['message' => ExpenseType::getMessage('expenses', 'update_success'), 'data' => $expenseType], 200);
	}

	/**
	 * Delete an expense type.
	 */
	public function deleteExpenseType($id)
	{
		$expenseTypeId = Expense::decrypt($id);

		if (!$expenseTypeId) {
			return response()->json(['error' => Expense::getMessage('common', 'invalid_request')], 400);
		}

		$expenseType = ExpenseType::find($expenseTypeId);

		if (!$expenseType) {
			return response()->json(['message' => ExpenseType::getMessage('expenses', 'not_found')], 404);
		}

		$expenseType->update(['is_active' => 0]);  // Soft delete by setting status to 0 (Deleted)

		return response()->json(['message' => ExpenseType::getMessage('expenses', 'delete_success')], 200);
	}
}