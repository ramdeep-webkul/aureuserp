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
        Schema::create('attendances_overtimes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('employees_employees')->restrictOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->restrictOnDelete();
            $table->date('date');
            $table->double('duration')->default(0);
            $table->double('duration_real')->default(0);
            $table->boolean('adjustment')->default(false);
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->index(['employee_id', 'date']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances_overtimes');
    }
};
