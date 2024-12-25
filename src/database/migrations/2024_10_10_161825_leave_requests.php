<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeaveRequests extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('leave_requests', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
			$table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('cascade');
			$table->foreignId('approver_user_id')->nullable()->constrained('users')->onDelete('set null');
			$table->date('start_date');
			$table->date('end_date');
			$table->string('status')->default('pending');  // Leave status: `pending`, `approved`, `rejected`
			$table->string('message')->nullable(); // optional message by employee
			$table->date('approver_date')->nullable();
			$table->string('approver_message')->nullable(); // optional message by approver
			$table->integer('total_days');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('leave_requests');
	}
};
