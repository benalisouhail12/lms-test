<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Table principale des devoirs
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('available_from')->nullable();
            $table->integer('max_points')->default(100);
            $table->integer('passing_grade')->nullable();
            $table->boolean('allow_late_submissions')->default(false);
            $table->integer('late_submission_penalty')->nullable(); // pourcentage de pénalité
            $table->boolean('enable_plagiarism_detection')->default(false);
            $table->json('allowed_file_types')->nullable();
            $table->integer('max_file_size')->nullable(); // en Mo
            $table->integer('max_attempts')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->boolean('is_group_assignment')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour les versions des devoirs
        Schema::create('assignment_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->integer('version_number');
            $table->text('changes_description')->nullable();
            $table->json('content_diff')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['assignment_id', 'version_number']);
        });

        // Table pour les soumissions de devoirs
      // Table pour les soumissions de devoirs
Schema::create('assignment_submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->unsignedBigInteger('group_id')->nullable();
    $table->text('submission_text')->nullable();
    $table->json('submitted_files')->nullable();
    $table->integer('attempt_number')->default(1);
    $table->string('status')->default('SUBMITTED'); // DRAFT, SUBMITTED, GRADED, RETURNED
    $table->timestamp('submitted_at')->nullable();
    $table->boolean('is_late')->default(false);
    $table->float('similarity_score')->nullable(); // score de plagiat (pourcentage)
    $table->timestamps();
    $table->softDeletes();

    // Use a shorter index name
    $table->unique(['assignment_id', 'user_id', 'attempt_number'], 'assignment_submission_unique');
});


        // Table pour les versions des soumissions
     // Table pour les versions des soumissions
Schema::create('submission_versions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_submission_id')->constrained()->onDelete('cascade');
    $table->integer('version_number');
    $table->text('submission_text')->nullable();
    $table->json('submitted_files')->nullable();
    $table->timestamp('submitted_at');
    $table->timestamps();

    // Use a shorter index name for the unique constraint
    $table->unique(['assignment_submission_id', 'version_number'], 'submission_version_unique');
});


        // Table pour les notes
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained()->onDelete('cascade');
            $table->float('points_earned');
            $table->float('points_possible');
            $table->float('percentage')->nullable();
            $table->string('letter_grade')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->boolean('is_final')->default(false);
            $table->timestamps();
        });

        // Table pour les critères de notation (rubrique)
        Schema::create('grading_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('max_points');
            $table->integer('weight')->default(1); // poids du critère dans la note finale
            $table->timestamps();
        });

        // Table pour les résultats des critères de notation
        Schema::create('criteria_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            // Fix the typo in the table name, it should be `grading_criteria`, not `grading_criterias`
            $table->foreignId('grading_criteria_id')->constrained('grading_criteria')->onDelete('cascade');
            $table->float('points_earned');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['grade_id', 'grading_criteria_id']);
        });

        // Table pour les commentaires sur les soumissions
        Schema::create('submission_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->json('attachment')->nullable();
            $table->boolean('is_private')->default(false); // visible uniquement par les enseignants
            $table->unsignedBigInteger('parent_comment_id')->nullable();
            $table->foreign('parent_comment_id')->references('id')->on('submission_comments')->onDelete('cascade');
            $table->json('comment_location')->nullable(); // position dans le document (ligne, colonne, etc.)
            $table->timestamps();
        });

        // Table pour les rapports de plagiat
        Schema::create('plagiarism_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained()->onDelete('cascade');
            $table->float('similarity_score');
            $table->json('matched_sources')->nullable();
            $table->json('similarity_details')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
        });

        // Table pour les groupes d'étudiants (pour les devoirs de groupe)
        Schema::create('assignment_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Table pivot pour les membres du groupe
        Schema::create('assignment_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_leader')->default(false);
            $table->timestamps();

            $table->unique(['assignment_group_id', 'user_id']);
        });

        // Table pour les extensions individuelles
        Schema::create('assignment_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('extended_due_date');
            $table->text('reason')->nullable();
            $table->foreignId('granted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['assignment_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignment_extensions');
        Schema::dropIfExists('assignment_group_members');
        Schema::dropIfExists('assignment_groups');
        Schema::dropIfExists('plagiarism_reports');
        Schema::dropIfExists('submission_comments');
        Schema::dropIfExists('criteria_grades');
        Schema::dropIfExists('grading_criteria');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('submission_versions');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignment_versions');
        Schema::dropIfExists('assignments');
    }
};
