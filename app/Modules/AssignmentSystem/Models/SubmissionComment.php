<?php

namespace App\Modules\AssignmentSystem\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_submission_id', 'user_id', 'comment',
        'attachment', 'is_private', 'parent_comment_id', 'comment_location'
    ];

    protected $casts = [
        'attachment' => 'array',
        'comment_location' => 'array',
        'is_private' => 'boolean',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentComment()
    {
        return $this->belongsTo(SubmissionComment::class, 'parent_comment_id');
    }

    public function replies()
    {
        return $this->hasMany(SubmissionComment::class, 'parent_comment_id');
    }
}
