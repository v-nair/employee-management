<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserRoles extends Migration
{
	public function up(): void
	{
		Schema::create('user_roles', function (Blueprint $table) {
			$table->id();
			$table->string('name')->unique(); // Admin, Manager, Employee, HR, Finance
			$table->string('description')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('user_roles');
	}
};
