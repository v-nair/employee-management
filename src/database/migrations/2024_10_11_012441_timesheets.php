<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TimeSheets extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('timesheets', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
			$table->date('date'); // Date of the timesheet entry
			$table->time('clock_in')->nullable(); // Clock-in time
			$table->time('clock_out')->nullable(); // Clock-out time, nullable if not clocked out yet
			$table->integer('hours_worked')->default(0); // Total hours worked for the day
			$table->string('status')->default('pending'); // Timesheet status (`pending`, `approved`, `rejected`)
			$table->foreignId('approver_user_id')->nullable()->constrained('users')->onDelete('set null'); // Nullable foreign key to approver user
			$table->text('approver_message')->nullable(); // Optional approver message
			$table->timestamps(); // Created and updated timestamps
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('timesheets');
	}
};