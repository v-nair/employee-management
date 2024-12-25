<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveUserMapping extends BaseModel
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'leave_type_id',
		'allocated_leaves',
	];

	/**
	 * Relationship to User model.
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Relationship to LeaveType model.
	 */
	public function leaveType()
	{
		return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
	}
}