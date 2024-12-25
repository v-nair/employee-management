<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends BaseModel
{
	use HasFactory;
	protected $fillable = ['name', 'description'];

	public function users()
	{
		return $this->belongsToMany(User::class, 'user_role_mapping', 'role_id', 'user_id');
	}

	// Define relationship to permissions
	public function permissions()
	{
		return $this->belongsToMany(UserPermission::class, 'user_permissions_mapping', 'role_id', 'permission_id');
	}
}