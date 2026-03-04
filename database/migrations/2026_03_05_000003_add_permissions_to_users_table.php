<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_manage_employees')->default(false)->after('is_admin');
            $table->boolean('can_manage_payouts')->default(false)->after('can_manage_employees');
            $table->boolean('can_manage_attendance')->default(false)->after('can_manage_payouts');
            $table->boolean('can_view_reports')->default(false)->after('can_manage_attendance');
            $table->boolean('can_view_employee_profiles')->default(false)->after('can_view_reports');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_manage_employees',
                'can_manage_payouts',
                'can_manage_attendance',
                'can_view_reports',
                'can_view_employee_profiles',
            ]);
        });
    }
};
