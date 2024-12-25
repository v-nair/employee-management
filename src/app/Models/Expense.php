<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends BaseModel
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'expense_type_id',
		'amount',
		'description',
		'status',
		'approver_user_id',
		'approver_message',
	];

	/**
	 * Define relationship with the user.
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	/**
	 * Define relationship with the expense type.
	 */
	public function expenseType()
	{
		return $this->belongsTo(ExpenseType::class, 'expense_type_id');
	}

	/**
	 * Define relationship with the approver.
	 */
	public function approver()
	{
		return $this->belongsTo(User::class, 'approver_user_id');
	}
}