<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPermission extends BaseModel
{
	use HasFactory;

	protected $fillable = ['name', 'description', 'api_route'];

	// Define relationship to roles
	public function roles()
	{
		return $this->belongsToMany(UserRole::class, 'user_permissions_mapping', 'permission_id', 'role_id');
	}
}