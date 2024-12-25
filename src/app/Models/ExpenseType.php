<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseType extends BaseModel
{
	use HasFactory;

	protected $fillable = ['name', 'description', 'is_active'];

	/**
	 * Define relationship to expenses.
	 */
	public function expenses()
	{
		return $this->hasMany(Expense::class, 'expense_type_id');
	}
}