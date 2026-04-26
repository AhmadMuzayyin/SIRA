<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentPrediction;
use App\Models\StudentPreprocessing;
use App\Models\StudentViolation;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCriterion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiraModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_students(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $createResponse = $this->post(route('students.store'), [
            'nis' => '250001',
            'name' => 'Santri Uji',
            'gender' => 'L',
            'room' => 'A1',
            'status' => 'aktif',
        ]);

        $createResponse->assertRedirect();
        $this->assertDatabaseHas('students', ['nis' => '250001', 'name' => 'Santri Uji']);

        $student = Student::query()->where('nis', '250001')->firstOrFail();

        $updateResponse = $this->put(route('students.update', $student), [
            'nis' => '250001',
            'name' => 'Santri Uji Update',
            'gender' => 'L',
            'room' => 'A2',
            'status' => 'aktif',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Santri Uji Update', 'room' => 'A2']);
    }

    public function test_preprocessing_and_prediction_can_run(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        $criterion = ViolationCriterion::query()->create([
            'code' => 'KR',
            'name' => 'Kriteria Ringan',
            'category' => 'ringan',
            'weight' => 1,
        ]);

        $violation = Violation::query()->create([
            'violation_criterion_id' => $criterion->id,
            'code' => 'P001',
            'name' => 'Tidak ikut jamaah',
            'points' => 2,
        ]);

        StudentViolation::query()->create([
            'student_id' => $student->id,
            'violation_id' => $violation->id,
            'occurred_at' => now()->toDateString(),
            'notes' => 'Catatan uji',
        ]);

        $preprocessResponse = $this->post(route('preprocessing.run'));
        $preprocessResponse->assertRedirect();

        $predictionResponse = $this->post(route('predictions.run'));
        $predictionResponse->assertRedirect();

        $this->assertDatabaseHas('student_preprocessings', ['student_id' => $student->id]);
        $this->assertDatabaseHas('student_predictions', ['student_id' => $student->id]);

        $preprocessing = StudentPreprocessing::query()->where('student_id', $student->id)->firstOrFail();
        $prediction = StudentPrediction::query()->where('student_id', $student->id)->firstOrFail();

        $this->assertIsArray($preprocessing->feature_vector);
        $this->assertGreaterThanOrEqual(0, $prediction->rank_score);
    }

    public function test_reports_and_exports_can_be_accessed(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Export Laporan');

        $this->get(route('students.export'))->assertOk();
        $this->get(route('student-violations.export'))->assertOk();
        $this->get(route('reports.excel'))->assertOk();
        $this->get(route('reports.pdf'))->assertOk();
    }
}
