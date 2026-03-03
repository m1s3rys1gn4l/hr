<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('employee_code')->nullable()->unique();
            $table->unsignedSmallInteger('released_employee_code')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->decimal('daily_salary', 10, 2);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->enum('status', ['active', 'left'])->default('active');
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
