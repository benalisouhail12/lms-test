<?php

namespace App\Modules\AssignmentSystem\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AssignmentSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assignment_id', 'user_id', 'group_id', 'submission_text',
        'submitted_files', 'attempt_number', 'status',
        'submitted_at', 'is_late', 'similarity_score'
    ];

    protected $casts = [
        'submitted_files' => 'array',
        'submitted_at' => 'datetime',
        'is_late' => 'boolean',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(AssignmentGroup::class);
    }

    public function versions()
    {
        return $this->hasMany(SubmissionVersion::class);
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }

    public function comments()
    {
        return $this->hasMany(SubmissionComment::class);
    }

    public function plagiarismReport()
    {
        return $this->hasOne(PlagiarismReport::class);
    }
}
