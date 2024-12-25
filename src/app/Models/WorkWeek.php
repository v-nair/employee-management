<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkWeek extends BaseModel
{
	use HasFactory;

	protected $fillable = [
		'day', 'default_start_time', 'default_end_time', 'hours_per_day'
	];
}