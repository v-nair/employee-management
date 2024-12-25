<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserPermissions extends Migration
{
	public function up(): void
	{
		Schema::create('user_permissions', function (Blueprint $table) {
			$table->id();
			$table->string('name')->unique(); // eg: view_employee, add_employee, edit_employee, delete_employee
			$table->string('description')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('user_permissions');
	}
};
