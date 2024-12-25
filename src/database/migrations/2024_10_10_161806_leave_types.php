<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeaveTypes extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('leave_types', function (Blueprint $table) {
			$table->id();
			$table->string('name')->unique(); // Casual Leave, Sick Leave
			$table->integer('default_value');
			$table->tinyInteger('is_active')->default(1); // 1-> active; 0 is deleted; 2-> inactive
			$table->string('description')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('leave_types');
	}
};
