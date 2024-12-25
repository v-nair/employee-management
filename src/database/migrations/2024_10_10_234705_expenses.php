<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Expenses extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('expenses', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
			$table->foreignId('expense_type_id')->constrained('expense_types')->onDelete('cascade');
			$table->decimal('amount', 8, 2);
			$table->text('description')->nullable();
			$table->string('status')->default('pending'); // `pending`, `approved`, `rejected`
			$table->foreignId('approver_user_id')->nullable()->constrained('users')->onDelete('set null');
			$table->text('approver_message')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('expenses');
	}
};