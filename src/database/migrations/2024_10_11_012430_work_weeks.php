<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkWeeks extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('work_weeks', function (Blueprint $table) {
			$table->id();
			$table->string('day'); // Day of the week (e.g., "Monday", "Tuesday")
			$table->time('default_start_time'); // Default start time (e.g., "09:00:00")
			$table->time('default_end_time'); // Default end time (e.g., "17:00:00")
			$table->integer('hours_per_day'); // Number of hours worked per day
			$table->timestamps(); // Created and updated timestamps
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('work_weeks');
	}
};