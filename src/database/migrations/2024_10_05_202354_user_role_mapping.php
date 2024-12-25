<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserRoleMapping extends Migration
{
	public function up(): void
	{
		Schema::create('user_role_mapping', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Link to permissions table
			$table->foreignId('role_id')->constrained('user_roles')->onDelete('cascade');  // Link to roles table
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('user_role_mapping');
	}
};
