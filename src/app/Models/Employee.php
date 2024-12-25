<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Employee extends BaseModel
{
	use HasFactory;

	protected $fillable = ['first_name', 'last_name', 'email', 'phone_number', 'department', 'hire_date'];
}