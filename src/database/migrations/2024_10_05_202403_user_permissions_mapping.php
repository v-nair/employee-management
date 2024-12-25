<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserPermissionsMapping extends Migration
{
	public function up(): void
	{
		Schema::create('user_permissions_mapping', function (Blueprint $table) {
			$table->id();
			$table->foreignId('role_id')->constrained('user_roles')->onDelete('cascade');  // Link to roles table
			$table->foreignId('permission_id')->constrained('user_permissions')->onDelete('cascade');  // Link to permissions table
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('user_permissions_mapping');
	}
};
