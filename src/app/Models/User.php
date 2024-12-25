<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'password' => 'hashed',
	];

	// Define relationship to roles
	public function roles()
	{
		return $this->belongsToMany(
			UserRole::class,			// The related model class for roles
			'user_role_mapping',		// The mapping table name
			'user_id',					// Foreign key in the mapping table (user_role_mapping)
			'role_id'					// Foreign key that references roles (user_roles)
		)->with('permissions');
	}

	/**
	 * Check if the user has a specific role.
	 *
	 * @param string $role
	 * @return bool
	 */
	public function hasRole($role)
	{
		return $this->roles()->where('name', $role)->exists();
	}
}