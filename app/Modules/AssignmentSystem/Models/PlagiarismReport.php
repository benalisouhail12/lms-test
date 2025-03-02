<?php

namespace App\Modules\AssignmentSystem\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlagiarismReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_submission_id', 'similarity_score',
        'matched_sources', 'similarity_details', 'checked_at'
    ];

    protected $casts = [
        'matched_sources' => 'array',
        'similarity_details' => 'array',
        'checked_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }
}
