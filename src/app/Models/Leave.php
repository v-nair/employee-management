<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends BaseModel
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'leave_type_id',
		'start_date',
		'end_date',
		'total_days',
		'status',
		'message',
		'approver_user_id',
		'approver_date',
		'approver_message',
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
		return $this->belongsTo(LeaveType::class);
	}
}