<?php

namespace App\Modules\CourseManagement\Tests\Feature;

use App\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\CourseManagement\Database\Factories\CourseFactory;
use App\Modules\CourseManagement\Database\Factories\LessonFactory;
use App\Modules\CourseManagement\Database\Factories\SectionFactory;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test for listing all courses
     */
    public function testIndex()
    {
        $course = CourseFactory::new()->create();

        $response = $this->get('/api/course/courses');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $course->title]);
    }

    /**
     * Test for creating a course
     */
   /**
 * Test for creating a course
 */
public function testStore()
{
    Role::firstOrCreate(['name' => 'instructor']);

    // Create a user with necessary permissions/role
    $user = UserFactory::new()->assignRoleAfterCreation()->create(); // Ensure the user has the 'admin' role

    $this->actingAs($user,'sanctum');



    // Make sure you have all required fields in your course data
    $courseData = CourseFactory::new()->make()->toArray();

    // You might need to add additional fields required by validation
    $courseData['instructor_ids'] = [$user->id]; // If your app requires instructor assignment

    // If you have department or program requirements
    // $courseData['department_id'] = $departmentId;
    // $courseData['program_id'] = $programId;

    $response = $this->postJson('/api/course/courses', $courseData);

    // Check if there are validation errors in the response
    if ($response->status() === 422) {
        dd($response->json()); // This will show validation errors
    }

    $response->assertStatus(201);
    $response->assertJsonFragment(['title' => $courseData['title']]);
}
  /**
     * Test for showing a specific course
     */
    public function testShow()
    {
        $course = CourseFactory::new()->create();

        $response = $this->getJson("/api/course/courses/{$course->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $course->title]);
    }

    /**
     * Test for updating a course
     */
    public function testUpdate()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $course = CourseFactory::new()->create();
        $updateData = ['title' => 'Updated Course'];

        $response = $this->putJson("/api/course/courses/{$course->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment($updateData);
    }

    /**
     * Test for deleting a course
     */
    public function testDestroy()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $course = CourseFactory::new()->create();

        $response = $this->deleteJson("/api/course/courses/{$course->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    /**
     * Test for storing sections
     */
    public function testStoreSections()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $course = CourseFactory::new()->create();
        $sectionData = [
            'name' => 'Test Section',
            'position' => 1
        ];

        $response = $this->postJson("/api/course/courses/{$course->id}/sections", $sectionData);

        $response->assertStatus(201);
        $response->assertJsonFragment($sectionData);
    }

    /**
     * Test for updating a section
     */
    public function testUpdateSection()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $section = SectionFactory::new()->create();
        $updateData = ['name' => 'Updated Section'];

        $response = $this->putJson("/api/course/sections/{$section->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment($updateData);
    }

    /**
     * Test for deleting a section
     */
    public function testDestroySection()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $section = SectionFactory::new()->create();

        $response = $this->deleteJson("/api/course/sections/{$section->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    /**
     * Test for reordering sections
     */
    public function testReorderSections()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $course = CourseFactory::new()->create();
        $section1 = SectionFactory::new()->create([
            'course_id' => $course->id,
            'position' => 1
        ]);
        $section2 = SectionFactory::new()->create([
            'course_id' => $course->id,
            'position' => 2
        ]);

        $reorderData = [
            'sections' => [
                ['id' => $section1->id, 'position' => 2],
                ['id' => $section2->id, 'position' => 1]
            ]
        ];

        $response = $this->postJson("/api/course/courses/{$course->id}/sections/reorder", $reorderData);

        $response->assertStatus(200);
    }

    /**
     * Test for storing lessons
     */
    public function testStoreLessons()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $section = SectionFactory::new()->create();
        $lessonData = [
            'title' => 'Test Lesson',
            'description' => 'Test Lesson Description',
            'content' => 'Test content',
            'lesson_type' => 'VIDEO',
            'position' => 1
        ];

        $response = $this->postJson("/api/course/sections/{$section->id}/lessons", $lessonData);

        $response->assertStatus(201);
        $response->assertJsonFragment(['title' => $lessonData['title']]);
    }

    /**
     * Test for updating a lesson
     */
    public function testUpdateLesson()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $lesson = LessonFactory::new()->create();
        $updateData = ['title' => 'Updated Lesson'];

        $response = $this->putJson("/api/course/lessons/{$lesson->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment($updateData);
    }

    /**
     * Test for deleting a lesson
     */
    public function testDestroyLesson()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $lesson = LessonFactory::new()->create();

        $response = $this->deleteJson("/api/course/lessons/{$lesson->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    /**
     * Test for reordering lessons
     */
    public function testReorderLessons()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $section = SectionFactory::new()->create();
        $lesson1 = LessonFactory::new()->create([
            'section_id' => $section->id,
            'position' => 1
        ]);
        $lesson2 = LessonFactory::new()->create([
            'section_id' => $section->id,
            'position' => 2
        ]);

        $reorderData = [
            'lessons' => [
                ['id' => $lesson1->id, 'position' => 2],
                ['id' => $lesson2->id, 'position' => 1]
            ]
        ];

        $response = $this->postJson("/api/course/sections/{$section->id}/lessons/reorder", $reorderData);

        $response->assertStatus(200);
    }

    /**
     * Test for enrolling a student
     */
    public function testEnrollStudent()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $course = CourseFactory::new()->create();
        $student = UserFactory::new()->create();

        $response = $this->postJson("/api/course/courses/{$course->id}/enroll", ['user_id' => $student->id]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'ACTIVE'
        ]);
    }

    /**
     * Test for unenrolling a student
     */
    public function testUnenrollStudent()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $course = CourseFactory::new()->create();
        $student = UserFactory::new()->create();

        // First enroll the student
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'ACTIVE',
            'enrolled_at' => now(),
            'progress_percentage' => 0
        ]);

        $response = $this->postJson("/api/course/courses/{$course->id}/unenroll", ['user_id' => $student->id]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'DROPPED'
        ]);
    }

    /**
     * Test for updating lesson progress
     */
    public function testUpdateProgress()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $lesson = LessonFactory::new()->create();

        $progressData = [
            'status' => 'COMPLETED',
            'time_spent' => 300
        ];

        $response = $this->postJson("/api/course/lessons/{$lesson->id}/progress", $progressData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'COMPLETED'
        ]);
    }

    /**
     * Test for getting student progress
     */
    public function testGetStudentProgress()
    {
        $user = UserFactory::new()->create();
        $this->actingAs($user, 'sanctum');

        $course = CourseFactory::new()->create();

        // Enroll the user in the course
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'ACTIVE',
            'enrolled_at' => now(),
            'progress_percentage' => 0
        ]);

        $response = $this->getJson("/api/course/courses/{$course->id}/progress");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'lessons_progress',
            'course_progress'
        ]);
    }


}
