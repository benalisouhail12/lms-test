<?php

namespace App\Modules\AssignmentSystem\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_submission_id', 'version_number',
        'submission_text', 'submitted_files', 'submitted_at'
    ];

    protected $casts = [
        'submitted_files' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }
}
