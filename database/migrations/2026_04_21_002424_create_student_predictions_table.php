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
        Schema::create('student_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->decimal('risk_probability', 6, 4)->default(0);
            $table->boolean('predicted_to_reoffend')->default(false)->index();
            $table->unsignedInteger('rank_score')->default(0)->index();
            $table->foreignId('suggested_violation_id')->nullable()->constrained('violations')->nullOnDelete();
            $table->json('evidence')->nullable();
            $table->timestamp('predicted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_predictions');
    }
};
