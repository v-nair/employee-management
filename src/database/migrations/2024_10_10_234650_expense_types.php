<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpenseTypes extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('expense_types', function (Blueprint $table) {
			$table->id();
			$table->string('name')->unique();
			$table->string('description')->nullable();
			$table->tinyInteger('is_active')->default(1); // 1: active, 0: deleted, 2: inactive
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('expense_types');
	}
};