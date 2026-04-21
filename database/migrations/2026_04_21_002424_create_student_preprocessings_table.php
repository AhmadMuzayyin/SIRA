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
        Schema::create('student_preprocessings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->unsignedInteger('total_violations')->default(0);
            $table->unsignedInteger('ringan_count')->default(0);
            $table->unsignedInteger('sedang_count')->default(0);
            $table->unsignedInteger('berat_count')->default(0);
            $table->unsignedInteger('jamaah_absence_count')->default(0);
            $table->timestamp('last_violation_at')->nullable();
            $table->json('feature_vector')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_preprocessings');
    }
};
