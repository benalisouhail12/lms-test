<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Table des cours
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->json('learning_objectives')->nullable();
            $table->string('course_type'); // ONLINE, HYBRID, IN_PERSON
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('level')->nullable(); // BEGINNER, INTERMEDIATE, ADVANCED
            $table->integer('duration_in_weeks')->nullable();
            $table->integer('credit_hours')->nullable();
            $table->integer('capacity')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour les sections de cours
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('position')->default(0);
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour les leçons
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->integer('position')->default(0);
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->integer('estimated_duration')->nullable(); // en minutes
            $table->boolean('is_published')->default(false);
            $table->string('lesson_type')->default('TEXT'); // VIDEO, TEXT, QUIZ, ASSIGNMENT, etc.
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour les ressources
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('resource_type'); // PDF, VIDEO, LINK, etc.
            $table->string('external_url')->nullable();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        // Table pour les activités
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('activity_type'); // QUIZ, ASSIGNMENT, DISCUSSION, etc.
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(false);
            $table->integer('points')->default(0);
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });

        // Table pour le suivi de progression des leçons
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('NOT_STARTED'); // NOT_STARTED, IN_PROGRESS, COMPLETED
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent')->default(0); // en secondes
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });

        // Table pour les inscriptions aux cours
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('PENDING'); // PENDING, ACTIVE, COMPLETED, DROPPED
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->float('progress_percentage')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });

        // Table pour les instructeurs de cours
        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });

        // Table pour les parcours d'apprentissage
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour les cours dans les parcours d'apprentissage
        Schema::create('course_learning_path', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_path_id')->constrained()->onDelete('cascade');
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->unique(['course_id', 'learning_path_id']);
        });

        // Table pour les prérequis de cours
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('prerequisite_course_id');
            $table->foreign('prerequisite_course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['course_id', 'prerequisite_course_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('course_learning_path');
        Schema::dropIfExists('learning_paths');
        Schema::dropIfExists('course_instructors');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('courses');
    }
};
