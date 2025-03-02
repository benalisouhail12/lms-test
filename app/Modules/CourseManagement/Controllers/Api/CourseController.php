<?php

namespace App\Modules\CourseManagement\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\Section;
use App\Modules\CourseManagement\Models\Lesson;
use App\Modules\CourseManagement\Models\Enrollment;
use App\Modules\CourseManagement\Models\LessonProgress;
use App\Modules\CourseManagement\Resources\CourseResource;
use App\Modules\CourseManagement\Resources\SectionResource;
use App\Modules\CourseManagement\Resources\LessonResource;
use App\Modules\CourseManagement\Requests\CourseRequest;
use App\Modules\CourseManagement\Requests\SectionRequest;
use App\Modules\CourseManagement\Requests\LessonRequest;
use App\Modules\CourseManagement\Requests\EnrollmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // === Gestion de cours ===



    public function index(Request $request)
    {
        $query = Course::query();

        // Filtres
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->has('course_type')) {
            $query->where('course_type', $request->course_type);
        }

        // Pagination
        $courses = $query->paginate($request->per_page ?? 15);

        return CourseResource::collection($courses);
    }

    public function store(CourseRequest $request)
    {
        DB::beginTransaction();

        try {
            $course = Course::create($request->validated());

            // Associer aux instructeurs
            if ($request->has('instructor_ids')) {
                $course->instructors()->attach($request->instructor_ids);
            }

            // Téléchargement de médias
            if ($request->hasFile('thumbnail')) {
                $course->addMediaFromRequest('thumbnail')
                    ->toMediaCollection('course_thumbnail');
            }

            if ($request->hasFile('banner')) {
                $course->addMediaFromRequest('banner')
                    ->toMediaCollection('course_banner');
            }

            DB::commit();

            return new CourseResource($course);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la création du cours: ' . $e->getMessage()], 500);
        }
    }

    public function show(Course $course)
    {
        return 'ok';
        $course->load(['sections.lessons', 'instructors', 'department', 'program']);
        return new CourseResource($course);
    }

    public function update(CourseRequest $request, Course $course)
    {
        DB::beginTransaction();

        try {
            $course->update($request->validated());

            // Mettre à jour les instructeurs
            if ($request->has('instructor_ids')) {
                $course->instructors()->sync($request->instructor_ids);
            }

            // Mettre à jour les médias
            if ($request->hasFile('thumbnail')) {
                $course->addMediaFromRequest('thumbnail')
                    ->toMediaCollection('course_thumbnail');
            }

            if ($request->hasFile('banner')) {
                $course->addMediaFromRequest('banner')
                    ->toMediaCollection('course_banner');
            }

            DB::commit();

            return new CourseResource($course);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la mise à jour du cours: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(['message' => 'Cours supprimé avec succès']);
    }

    // === Gestion des sections ===
    public function storeSections(SectionRequest $request, Course $course)
    {
        $section = $course->sections()->create($request->validated());
        return new SectionResource($section);
    }

    public function updateSection(SectionRequest $request, Section $section)
    {
        $section->update($request->validated());
        return new SectionResource($section);
    }

    public function destroySection(Section $section)
    {
        $section->delete();
        return response()->json(['message' => 'Section supprimée avec succès']);
    }

    public function reorderSections(Request $request, Course $course)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:sections,id',
            'sections.*.position' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->sections as $sectionData) {
                $section = Section::findOrFail($sectionData['id']);
                $section->update(['position' => $sectionData['position']]);
            }

            DB::commit();

            return response()->json(['message' => 'Sections réorganisées avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la réorganisation des sections: ' . $e->getMessage()], 500);
        }
    }

    // === Gestion des leçons ===
    public function storeLessons(LessonRequest $request, Section $section)
    {
        DB::beginTransaction();

        try {
            $lesson = $section->lessons()->create($request->validated());

            // Téléchargement de médias
            if ($request->hasFile('media')) {
                $lesson->addMediaFromRequest('media')
                    ->toMediaCollection('lesson_media');
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $lesson->addMedia($attachment)
                        ->toMediaCollection('attachments');
                }
            }

            DB::commit();

            return new LessonResource($lesson);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la création de la leçon: ' . $e->getMessage()], 500);
        }
    }

    public function updateLesson(LessonRequest $request, Lesson $lesson)
    {
        DB::beginTransaction();

        try {
            $lesson->update($request->validated());

            // Mettre à jour les médias
            if ($request->hasFile('media')) {
                $lesson->clearMediaCollection('lesson_media');
                $lesson->addMediaFromRequest('media')
                    ->toMediaCollection('lesson_media');
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $lesson->addMedia($attachment)
                        ->toMediaCollection('attachments');
                }
            }

            DB::commit();

            return new LessonResource($lesson);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la mise à jour de la leçon: ' . $e->getMessage()], 500);
        }
    }

    public function destroyLesson(Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(['message' => 'Leçon supprimée avec succès']);
    }

    public function reorderLessons(Request $request, Section $section)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.position' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->lessons as $lessonData) {
                $lesson = Lesson::findOrFail($lessonData['id']);
                $lesson->update(['position' => $lessonData['position']]);
            }

            DB::commit();

            return response()->json(['message' => 'Leçons réorganisées avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la réorganisation des leçons: ' . $e->getMessage()], 500);
        }
    }

    // === Gestion des inscriptions ===
    public function enrollStudent(EnrollmentRequest $request, Course $course)
    {
        $enrollment = Enrollment::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'course_id' => $course->id,
            ],
            [
                'status' => 'ACTIVE',
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]
        );

        return response()->json([
            'message' => 'Étudiant inscrit avec succès',
            'enrollment' => $enrollment,
        ]);
    }

    public function unenrollStudent(Request $request, Course $course)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $enrollment = Enrollment::where('user_id', $request->user_id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Étudiant non inscrit à ce cours'], 404);
        }

        $enrollment->update(['status' => 'DROPPED']);

        return response()->json(['message' => 'Inscription supprimée avec succès']);
    }

    // === Suivi de progression ===
    public function updateProgress(Request $request, Lesson $lesson)
    {
        $request->validate([
            'status' => 'required|in:NOT_STARTED,IN_PROGRESS,COMPLETED',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();

        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'status' => $request->status,
                'time_spent' => DB::raw('time_spent + ' . ($request->time_spent ?? 0)),
                'viewed_at' => DB::raw('COALESCE(viewed_at, NOW())'),
                'completed_at' => $request->status === 'COMPLETED' ? now() : null,
            ]
        );

        // Mettre à jour le pourcentage global de progression du cours
        $section = $lesson->section;
        $course = $section->course;

        $totalLessons = $course->sections()
            ->withCount('lessons')
            ->get()
            ->sum('lessons_count');

        $completedLessons = LessonProgress::whereHas('lesson', function ($query) use ($course) {
                $query->whereHas('section', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                });
            })
            ->where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->count();

        $progressPercentage = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->update([
                'progress_percentage' => $progressPercentage,
                'completed_at' => $progressPercentage >= 100 ? now() : null,
                'status' => $progressPercentage >= 100 ? 'COMPLETED' : 'ACTIVE',
            ]);

        return response()->json([
            'message' => 'Progression mise à jour avec succès',
            'progress' => $progress,
            'course_progress' => $progressPercentage,
        ]);
    }

    public function getStudentProgress(Request $request, Course $course)
    {
        $user = Auth::user();

        $lessons = Lesson::whereHas('section', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->with(['progress' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return response()->json([
            'lessons_progress' => LessonResource::collection($lessons),
            'course_progress' => $enrollment ? $enrollment->progress_percentage : 0,
        ]);
    }
}
