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
        Schema::create('attendances_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('employees_employees')->restrictOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->restrictOnDelete();
            $table->dateTime('check_in');
            $table->dateTime('check_out')->nullable();
            $table->double('worked_hours')->default(0);
            $table->double('expected_hours')->default(0);
            $table->double('overtime_hours')->default(0);
            $table->double('validated_overtime_hours')->default(0);
            $table->string('overtime_status')->nullable();
            $table->string('in_mode')->default('manual');
            $table->string('out_mode')->nullable();
            $table->string('in_ip_address')->nullable();
            $table->string('in_browser')->nullable();
            $table->string('out_ip_address')->nullable();
            $table->string('out_browser')->nullable();
            $table->text('edit_reason')->nullable();
            $table->string('idempotency_key')->nullable()->unique();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->index(['employee_id', 'check_in']);
            $table->index(['employee_id', 'check_out']);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances_attendances');
    }
};
