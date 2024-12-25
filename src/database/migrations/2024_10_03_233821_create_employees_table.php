<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
	public function up(): void
	{
		Schema::create('employees', function (Blueprint $table) {
			$table->id();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('email')->unique();
			$table->string('phone_number')->nullable();
			$table->string('department');
			$table->date('hire_date');
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('employees');
	}
};
