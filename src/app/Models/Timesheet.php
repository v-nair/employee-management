<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends BaseModel
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'date',
		'clock_in',
		'clock_out',
		'hours_worked',
		'status',
		'approver_user_id',
		'approver_message'
	];

	// Define relationship to User
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	// Define relationship to Approver
	public function approver()
	{
		return $this->belongsTo(User::class, 'approver_user_id');
	}
}