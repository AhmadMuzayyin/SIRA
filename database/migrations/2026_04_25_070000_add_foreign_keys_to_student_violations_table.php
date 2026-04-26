<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_violations', function (Blueprint $table) {
            $databaseName = DB::getDatabaseName();

            $studentForeignExists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $databaseName)
                ->where('TABLE_NAME', 'student_violations')
                ->where('CONSTRAINT_NAME', 'student_violations_student_id_foreign')
                ->exists();

            if (! $studentForeignExists) {
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            }

            $violationForeignExists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $databaseName)
                ->where('TABLE_NAME', 'student_violations')
                ->where('CONSTRAINT_NAME', 'student_violations_violation_id_foreign')
                ->exists();

            if (! $violationForeignExists) {
                $table->foreign('violation_id')->references('id')->on('violations')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_violations', function (Blueprint $table) {
            $databaseName = DB::getDatabaseName();

            $studentForeignExists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $databaseName)
                ->where('TABLE_NAME', 'student_violations')
                ->where('CONSTRAINT_NAME', 'student_violations_student_id_foreign')
                ->exists();

            if ($studentForeignExists) {
                $table->dropForeign('student_violations_student_id_foreign');
            }

            $violationForeignExists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $databaseName)
                ->where('TABLE_NAME', 'student_violations')
                ->where('CONSTRAINT_NAME', 'student_violations_violation_id_foreign')
                ->exists();

            if ($violationForeignExists) {
                $table->dropForeign('student_violations_violation_id_foreign');
            }
        });
    }
};
