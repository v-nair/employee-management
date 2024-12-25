<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends BaseModel
{
	use HasFactory;

	protected $fillable = [
		'name',
		'default_value',
		'is_active',
		'description',
	];

	/**
	 * Relationship to Leave model.
	 */
	public function leaves()
	{
		return $this->hasMany(Leave::class);
	}

	/**
	 * Relationship to LeaveUserMapping model.
	 */
	public function leaveUserMappings()
	{
		return $this->hasMany(LeaveUserMapping::class, 'leave_type_id', 'id');
	}
}