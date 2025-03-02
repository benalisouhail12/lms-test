<?php

namespace App\Modules\AssignmentSystem\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id', 'version_number', 'changes_description',
        'content_diff', 'created_by'
    ];

    protected $casts = [
        'content_diff' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
