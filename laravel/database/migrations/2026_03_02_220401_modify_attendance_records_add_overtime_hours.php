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
        if (Schema::hasColumn('attendance_records', 'has_overtime')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropColumn('has_overtime');
            });
        }

        if (! Schema::hasColumn('attendance_records', 'overtime_hours')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->unsignedTinyInteger('overtime_hours')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attendance_records', 'overtime_hours')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropColumn('overtime_hours');
            });
        }

        if (! Schema::hasColumn('attendance_records', 'has_overtime')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->boolean('has_overtime')->default(false);
            });
        }
    }
};
